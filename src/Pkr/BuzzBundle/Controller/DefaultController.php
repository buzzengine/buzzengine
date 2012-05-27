<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\Author;
use Pkr\BuzzBundle\Entity\Domain;
use Pkr\BuzzBundle\Entity\FeedEntry;
use Pkr\BuzzBundle\Entity\Topic;
use Pkr\BuzzBundle\Filter;
use Zend\Feed\Reader\Entry;
use Zend\Feed\Reader\Reader;
use Zend\Date\Date;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/run")
     */
    public function runAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $topics = $em->getRepository('PkrBuzzBundle:Topic')->findAll();
        foreach ($topics as $topic)
        {
            $filterChain = array ();

            // weitere Filter via Topic
            // @todo: Filter BlackWhiteList = Filter/BlackWhiteList
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

        $em->flush();

        var_dump('run finished');
        die(__FILE__ . ' - ' . __LINE__);
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

    protected function _handleFeed(Topic $topic, $feed, array $filterChain = null)
    {
        // @todo: getUrl, getDisabled, typehint via interface

        if ($feed->getDisabled())
        {
            return;
        }

        try
        {
            // @todo: feed proxy object -> caching
            $feedObject = Reader::import($feed->getUrl());
        }
        catch (\Exception $e)
        {
            // @todo: log service
            var_dump($e->getMessage(), $feed->getUrl());

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
        $em = $this->getDoctrine()->getEntityManager();

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

        $errors = $this->get('validator')->validate($feedEntry);
        if (count($errors) > 0)
        {
            // @todo: log service
            var_dump($errors);
        }
        else
        {
            $em->persist($feedEntry);
        }
    }

    protected function _getAuthor($topic, $name)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $author = $em->getRepository('PkrBuzzBundle:Author')->findOneBy(
            array('name' => $name)
        );

        if (is_null($author))
        {
            $author = new Author();
            $author->setTopic($topic);
            $author->setName($name);

            $errors = $this->get('validator')->validate($author);
            if (count($errors) > 0)
            {
                // @todo: log service
                var_dump($errors);
            }
            else
            {
                $em->persist($author);
                $em->flush($author);

                return $author;
            }
        }
        else
        {
            return $author;
        }

        return null;
    }

    protected function _getDomain($topic, $url)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if (preg_match('~^((http|https)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}).*~', $url, $matches))
        {
            $url = $matches[1];

            $domain = $em->getRepository('PkrBuzzBundle:Domain')->findOneBy(
                array ('url' => $url)
            );

            if (is_null($domain))
            {
                $domain = new Domain();
                $domain->setTopic($topic);
                $domain->setUrl($url);

                $errors = $this->get('validator')->validate($domain);
                if (count($errors) > 0)
                {
                    // @todo: log service
                    var_dump($errors);
                }
                else
                {
                    $em->persist($domain);
                    $em->flush($domain);

                    return $domain;
                }
            }
            else
            {
                return $domain;
            }
        }

        return null;
    }
}
