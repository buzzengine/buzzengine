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
            // @todo: feed proxy object -> cache
            $feedObject = Reader::import($feed->getUrl());
        }
        catch (\Exception $e)
        {
            var_dump($e->getMessage(), $feed->getUrl());
            die(__FILE__ . ' - ' . __LINE__);
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
        $validator = $this->get('validator');
        $em = $this->getDoctrine()->getEntityManager();

        $authorRepository = $em->getRepository('PkrBuzzBundle:Author');
        $domainRepository = $em->getRepository('PkrBuzzBundle:Domain');

        $feedEntry = new FeedEntry();
        $feedEntry->setTitle($entry->getTitle());

        if (null !== $entry->getAuthors())
        {
            foreach ($entry->getAuthors()->getValues() as $name)
            {
                $author = $authorRepository->findOneBy(
                    array('name' => $name)
                );

                if (is_null($author))
                {
                    $author = new Author();
                    $author->setTopic($topic);
                    $author->setName($name);

                    $errors = $validator->validate($author);
                    if (count($errors) > 0)
                    {
                        var_dump($errors);
                    }
                    else
                    {
                        $em->persist($author);
                        $em->flush($author);

                        $feedEntry->getAuthors()->add($author);
                    }
                }
                else
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

        $url = $entry->getPermalink();
        if (!preg_match('~^((http|https)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}).*~', $url, $matches))
        {
            return;
        }
        $url = $matches[1];

        $domain = $domainRepository->findOneBy(
            array('url' => $url)
        );

        if (is_null($domain))
        {
            $domain = new Domain();
            $domain->setTopic($topic);
            $domain->setUrl($url);

            $errors = $validator->validate($domain);
            if (count($errors) > 0)
            {
                var_dump($errors);
            }
            else
            {
                $em->persist($domain);
                $em->flush($domain);

                $feedEntry->setDomain($domain);
            }
        }
        else
        {
            $feedEntry->setDomain($domain);
        }

        $feedEntry->setLinks($entry->getLinks());

        // @todo: datetime in w3c style? -> timezone

        $errors = $validator->validate($feedEntry);

        if (count($errors) > 0)
        {
            var_dump($errors);
        }
        else
        {
            $em->persist($feedEntry);
        }
    }
}
