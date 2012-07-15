<?php

namespace Pkr\BuzzBundle\Service;

use Doctrine\ORM\EntityManager;
use Pkr\BuzzBundle\Entity\AbstractFeed;
use Pkr\BuzzBundle\Entity\Author;
use Pkr\BuzzBundle\Entity\Domain;
use Pkr\BuzzBundle\Entity\FeedEntry;
use Pkr\BuzzBundle\Entity\Log;
use Pkr\BuzzBundle\Entity\Topic;
use Pkr\BuzzBundle\Entity\TopicFeed;
use Pkr\BuzzBundle\Filter;
use Symfony\Component\Validator\Validator;
use Zend\Feed\Reader\Entry;
use Zend\Feed\Reader\Reader;
use Zend\Date\Date;

class Feed
{
    const QUERY_FILTER_KEY = 'queryFilter';

    protected $_entityManager = null;
    protected $_feeds = array ();
    protected $_filterConfig = null;
    protected $_validator = null;

    public function __construct(EntityManager $em, Validator $validator, array $filterConfig)
    {
        $this->_entityManager = $em;
        $this->_validator = $validator;
        $this->_filterConfig = $filterConfig;
    }

    protected function _log($message, $level = Log::NOTICE)
    {
        $log = new Log();
        $log->setLevel($level);
        $log->setMessage($message);

        $errors = $this->_validator->validate($log);
        if (count($errors) > 0)
        {
            throw new \Exception('validate entity log failed');
        }

        $this->_entityManager->persist($log);
        $this->_entityManager->flush($log);
    }

    protected function _createQueryFilter(Topic $topic)
    {
        $queryFilter = new Filter\Query();
        foreach ($topic->getQueries() as $query)
        {
            if ($query->getDisabled())
            {
                continue;
            }

            $queryFilter->addQuery($query);
        }

        return $queryFilter;
    }

    protected function _getFeed($url)
    {
        if (!array_key_exists($url, $this->_feeds))
        {
            try
            {
                $this->_feeds[$url] = Reader::import($url);
            }
            catch (\Exception $e)
            {
                $this->_log($e->getMessage() . ', import of "' . $url . '" failed', Log::WARNING);

                return null;
            }
        }

        return $this->_feeds[$url];
    }

    protected function _handleFeed(Topic $topic, AbstractFeed $feed, array $filterChain = null)
    {
        if ($feed->getDisabled())
        {
            return;
        }

        $feedObject = $this->_getFeed($feed->getUrl());
        if (is_null($feedObject))
        {
            return;
        }

        foreach ($feedObject as $feedEntryObject)
        {
            if (!is_null($filterChain))
            {
                foreach ($filterChain as $filter)
                {
                    if (!$filter->isAccepted($feedEntryObject))
                    {
                        continue (2);
                    }
                }
            }

            $feedEntry = $this->_entityManager
                          ->getRepository('PkrBuzzBundle:FeedEntry')
                          ->findOneBy(array ('title'   => $feedEntryObject->getTitle(),
                                             'content' => $feedEntryObject->getContent()));

            if (is_null($feedEntry))
            {
                $feedEntry = new FeedEntry();
                $feedEntry->setTitle($feedEntryObject->getTitle());

                if (null !== $feedEntryObject->getAuthors())
                {
                    foreach ($feedEntryObject->getAuthors()->getValues() as $name)
                    {
                        $author = $this->_getAuthor($topic, $name);
                        if (!is_null($author))
                        {
                            $feedEntry->getAuthors()->add($author);
                        }
                    }
                }

                $feedEntry->setDescription($feedEntryObject->getDescription());
                $feedEntry->setContent($feedEntryObject->getContent());

                $dateCreated = $feedEntryObject->getDateCreated();
                if ($dateCreated)
                {
                    $dateCreated = new \DateTime($dateCreated->get(Date::W3C));
                }
                else
                {
                    $dateCreated = new \DateTime();
                }
                $feedEntry->setDateCreated($dateCreated);

                $dateModified = $feedEntryObject->getDateModified();
                if ($dateModified)
                {
                    $dateModified = new \DateTime($dateModified->get(Date::W3C));
                }
                else
                {
                    $dateModified = $dateCreated;
                }
                $feedEntry->setDateModified($dateModified);

                $domain = $this->_getDomain($topic, $feedEntryObject->getPermalink());
                if (!is_null($domain))
                {
                    $feedEntry->setDomain($domain);
                }

                $feedEntry->setLinks($feedEntryObject->getLinks());

                $queryFilter = $filterChain[self::QUERY_FILTER_KEY];
                foreach ($queryFilter->getMatchedQueries() as $query)
                {
                    $feedEntry->getQueries()->add($query);
                }

                // @todo: datetime in w3c style? -> timezone

                $errors = $this->_validator->validate($feedEntry);
                if (count($errors) > 0)
                {
                    foreach ($errors as $error)
                    {
                        $this->_log('FeedEntry: ' . $error->getMessage() . ': ' . $feedEntryObject->getTitle(), Log::NOTICE);
                    }

                    return;
                }
            }
            else
            {
                $queries = $feedEntry->getQueries();
                $queryFilter = $filterChain[self::QUERY_FILTER_KEY];

                foreach ($queryFilter->getMatchedQueries() as $query)
                {
                    if (!$queries->contains($query))
                    {
                        $feedEntry->getQueries()->add($query);
                    }
                }
            }

            $this->_entityManager->persist($feedEntry);
        }
    }

    protected function _getAuthor($topic, $name)
    {
        $author = $this->_entityManager->getRepository('PkrBuzzBundle:Author')->findOneBy(
            array('name' => $name)
        );

        if (is_null($author))
        {
            $author = new Author();
            $author->setTopic($topic);
            $author->setName($name);

            $errors = $this->_validator->validate($author);
            if (count($errors) > 0)
            {
                foreach ($errors as $error)
                {
                    $this->_log('Author: ' . $error->getMessage() . ': ' . $name, Log::NOTICE);
                }

                return null;
            }

            $this->_entityManager->persist($author);
            $this->_entityManager->flush($author);
        }

        return $author;
    }

    protected function _getDomain($topic, $url)
    {
        $url = $this->_extractDomain($url);

        if (is_null($url))
        {
            return null;
        }

        $domain = $this->_entityManager->getRepository('PkrBuzzBundle:Domain')->findOneBy(
            array ('url' => $url)
        );

        if (is_null($domain))
        {
            $domain = new Domain();
            $domain->setTopic($topic);
            $domain->setUrl($url);

            $errors = $this->_validator->validate($domain);
            if (count($errors) > 0)
            {
                foreach ($errors as $error)
                {
                    $this->_log('Domain: ' . $error->getMessage() . ': ' . $url, Log::NOTICE);
                }

                return null;
            }

            $this->_entityManager->persist($domain);
            $this->_entityManager->flush($domain);
        }

        return $domain;
    }

    protected function _extractDomain($url)
    {
        $feedProxyDomains = array (
            'http://feedproxy.google.com',
            'http://rss.feedsportal.com'
        );

        $pattern = '~^((http|https)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}).*~';

        $matches = array ();
        if (!preg_match($pattern, $url, $matches))
        {
            return null;
        }

        $filteredUrl = $matches[1];

        if (in_array($filteredUrl, $feedProxyDomains))
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_NOBODY, true);

            if (false === curl_exec($curl))
            {
                $this->_log('curl_exec failed for url "' . $url . '"', Log::WARNING);

                return null;
            }

            $info = curl_getinfo($curl);

            if (!preg_match($pattern, $info['redirect_url'], $matches))
            {
                return null;
            }

            $filteredUrl = $matches[1];
        }

        return $filteredUrl;
    }

    public function fetch($value = null)
    {
        $topicRepository = $this->_entityManager->getRepository('PkrBuzzBundle:Topic');

        if (is_numeric($value))
        {
            $topics = $topicRepository->findById($value);
        }
        else
        {
            $topics = $topicRepository->findAll();
        }

        foreach ($topics as $topic)
        {
            $filterChain = array ();
            $filterChain[self::QUERY_FILTER_KEY] = $this->_createQueryFilter($topic);

            foreach ($topic->getFilters() as $filter)
            {
                if ($filter->getDisabled())
                {
                    continue;
                }

                switch ($filter->getClass())
                {
                    case 'Filter\Regex':
                        $filterChain[] = new Filter\Regex($filter->getValue());
                        break;
                    case 'Filter\Language\DetectlanguageCom':
                        if (empty($this->_filterConfig['language']['detectlanguageCom']['apiKey']))
                        {
                            $this->_log('Filter: detectlanguage.com api key missing', Log::NOTICE);
                            break;
                        }

                        $filterChain[] = new Filter\Language\DetectlanguageCom(
                            $this->_filterConfig['language']['detectlanguageCom']['apiKey'],
                            $filter->getAllowedLanguages()
                        );
                        break;
                    default:
                        throw new Filter\UnknownFilterException();
                }
            }

            $fetchFrequency = null;
            if (!(is_null($value) || is_numeric($value)))
            {
                $fetchFrequency = $value;
            }

            foreach ($topic->getTopicFeeds($fetchFrequency) as $feed)
            {
                $this->_handleFeed($topic, $feed, $filterChain);
            }

            foreach ($topic->getCategories() as $category)
            {
                foreach ($category->getFeeds($fetchFrequency) as $feed)
                {
                    $this->_handleFeed($topic, $feed, $filterChain);
                }
            }
        }

        $this->_entityManager->flush();
    }
}
