<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Pkr\BuzzBundle\Entity\FeedEntry;
use Pkr\BuzzBundle\Entity\TopicFeed;
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

        $entities = $em->getRepository('PkrBuzzBundle:TopicFeed')->findBy(array('disabled' => false));

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
                # ~(?=.*JavaScript)(?=.*Raphaël)(?!.*lolo)~
                #$pattern = '~(?=.*Android)(?!.*Microsoft)~i';

                #if (preg_match($pattern, $entry->getDescription()))
                #{
                    $feedEntry = new FeedEntry();
                    $feedEntry->setTitle($entry->getTitle());
                    $feedEntry->setAuthors($entry->getAuthors()->getValues());
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
                #}
            }
        }

        $em->flush();

        var_dump('run finished');
        die(__FILE__ . ' - ' . __LINE__);
    }
}
