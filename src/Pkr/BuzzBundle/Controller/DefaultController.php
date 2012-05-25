<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\Author;
use Pkr\BuzzBundle\Entity\Domain;
use Pkr\BuzzBundle\Entity\FeedEntry;
use Pkr\BuzzBundle\Entity\TopicFeed;
use Pkr\BuzzBundle\Filter;
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

        $this->_fetchFeeds();
        #$this->_fetchTopicFeeds();

        $em->flush();

        var_dump('run finished');
        die(__FILE__ . ' - ' . __LINE__);
    }

    protected function _fetchFeeds()
    {
        $validator = $this->get('validator');
        $em = $this->getDoctrine()->getEntityManager();

        $authorRepository = $em->getRepository('PkrBuzzBundle:Author');
        $domainRepository = $em->getRepository('PkrBuzzBundle:Domain');

        $entities = $em->getRepository('PkrBuzzBundle:Feed')->findBy(
            array('disabled' => false)
        );

        foreach ($entities as $entity)
        {
            try
            {
                $feed = Reader::import($entity->getUrl());
            }
            catch (Exception $e)
            {
                var_dump($e->getMessage());
                die(__FILE__ . ' - ' . __LINE__);
            }

            foreach ($feed as $entry)
            {
                foreach ($entity->getCategory()->getTopics() as $topic)
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

                    $filterChain = array();
                    $filterChain[] = $queryFilter;

                    // Filter an Topic
                    // TODO: Filter BlackWhiteList = Filter/BlackWhiteList
                    // TODO: Filter Sprache = Filter/Language
                    // done: Filter Dupletten = Filter/Duplicate

                    foreach ($filterChain as $filter)
                    {
                        if (!$filter->isAccepted($entry))
                        {
                            continue (2);
                        }
                    }

                    $feedEntry = new FeedEntry();
                    $feedEntry->setTitle($entry->getTitle());

                    if (null != $entry->getAuthors())
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
                        continue;
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
        }
    }

    protected function _fetchTopicFeeds()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PkrBuzzBundle:TopicFeed')->findBy(
            array('disabled' => false)
        );

        foreach ($entities as $entity)
        {
            try
            {
                $feed = Reader::import($entity->getUrl());
            }
            catch (Exception $e)
            {
                var_dump($e->getMessage());
                die(__FILE__ . ' - ' . __LINE__);
            }

            foreach ($feed as $entry)
            {
                $feedEntry = new FeedEntry();
                $feedEntry->setTitle($entry->getTitle());

                if (null != $entry->getAuthors())
                {
                    $feedEntry->setAuthors($entry->getAuthors()->getValues());
                }

                $feedEntry->setDescription($entry->getDescription());
                $feedEntry->setContent($entry->getContent());

                $dateCreated = new \DateTime($entry->getDateCreated()->get(Date::W3C));
                $feedEntry->setDateCreated($dateCreated);

                $dateModified = new \DateTime($entry->getDateModified()->get(Date::W3C));
                $feedEntry->setDateModified($dateModified);

                $feedEntry->setPermalink($entry->getPermalink());
                $feedEntry->setLinks($entry->getLinks());

                // @todo: Validation
                // @todo: datetime in w3c style? -> timezone

                $em->persist($feedEntry);
            }
        }
    }
}
