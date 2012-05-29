<?php

namespace Pkr\BuzzBundle\Service;

use Doctrine\ORM\EntityManager;
use Pkr\BuzzBundle\Entity\Author;
use Pkr\BuzzBundle\Entity\Domain;
use Pkr\BuzzBundle\Entity\FeedEntry;
use Pkr\BuzzBundle\Entity\Topic;
use Pkr\BuzzBundle\Filter;
use Symfony\Component\Validator\Validator;
use Zend\Feed\Reader\Entry;
use Zend\Feed\Reader\Reader;
use Zend\Date\Date;

class Feed
{
    protected $_entityManager = null;
    protected $_feeds = array ();

    public function __construct(EntityManager $em, Validator $validator)
    {
        $this->_entityManager = $em;
        $this->_validator = $validator;
    }

    public function run()
    {
        $topics = $this->_entityManager->getRepository('PkrBuzzBundle:Topic')
                                       ->findAll();
        foreach ($topics as $topic)
        {
            $filterChain = array ();
            # $filterChain[] = new Filter\Language(array ('de', 'en'));

            // weitere Filter via Topic
            // @todo: Filter BlackWhiteList = Filter/BlackWhiteList
            // @todo: Filter Regex = Filter/Regex
            // @todo: Filter Sprache = Filter/Language
            // @todo: Filter Dupletten = Filter/Duplicate ?

            foreach ($topic->getTopicFeeds() as $feed)
            {
                $this->_handleFeed($topic, $feed, $filterChain);
            }

            $filterChain[] = $this->_createQueryFilter($topic);

            foreach ($topic->getCategories() as $category)
            {
                foreach ($category->getFeeds() as $feed)
                {
                    $this->_handleFeed($topic, $feed, $filterChain);
                }
            }
        }

        $this->_entityManager->flush();
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

            $queryFilter->addQuery($query->getValue());
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
                // @todo: log service
                var_dump($e->getMessage(), $url);

                return null;
            }
        }

        return $this->_feeds[$url];
    }

    protected function _handleFeed(Topic $topic, $feed, array $filterChain = null)
    {
        // @todo: getUrl, getDisabled, typehint via interface

        if ($feed->getDisabled())
        {
            return;
        }

        $feedObject = $this->_getFeed($feed->getUrl());
        if (is_null($feedObject))
        {
            return;
        }

        foreach ($feedObject as $feedEntry)
        {
            if (!is_null($filterChain))
            {
                foreach ($filterChain as $filter)
                {
                    if (!$filter->isAccepted($feedEntry))
                    {
                        continue (2);
                    }
                }
            }

            $this->_persistFeedEntry($topic, $feedEntry);
        }
    }

    protected function _persistFeedEntry(Topic $topic, Entry $entry)
    {
        $feedEntry = new FeedEntry();
        $feedEntry->setTitle($entry->getTitle());

        if (null !== $entry->getAuthors())
        {
            foreach ($entry->getAuthors()->getValues() as $name)
            {
                $author = $this->_getAuthor($topic, $name);
                if (!is_null($author))
                {
                    $feedEntry->getAuthors()->add($author);
                }
            }
        }

        $feedEntry->setDescription($entry->getDescription());
        $feedEntry->setContent($entry->getContent());

        $dateCreated = new \DateTime($entry->getDateCreated()->get(Date::W3C));
        $feedEntry->setDateCreated($dateCreated);

        $dateModified = new \DateTime($entry->getDateModified()->get(Date::W3C));
        $feedEntry->setDateModified($dateModified);

        $domain = $this->_getDomain($topic, $entry->getPermalink());
        if (!is_null($domain))
        {
            $feedEntry->setDomain($domain);
        }

        $feedEntry->setLinks($entry->getLinks());

        // @todo: datetime in w3c style? -> timezone

        $errors = $this->_validator->validate($feedEntry);
        if (count($errors) > 0)
        {
            // @todo: log service
            var_dump($errors);
            return;
        }

        $this->_entityManager->persist($feedEntry);
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
                // @todo: log service
                var_dump($errors);
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
                // @todo: log service
                var_dump($errors);
                return null;
            }

            $this->_entityManager->persist($domain);
            $this->_entityManager->flush($domain);
        }

        return $domain;
    }

    protected function _extractDomain($url)
    {
        $feedProxyDomains = array ('http://feedproxy.google.com');

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
                var_dump('curl_exec failed');
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
}
