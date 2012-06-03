<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\FeedEntry;

/**
 * Feed controller.
 *
 * @Route("/feed-entry")
 */
class FeedEntryController extends Controller
{
    /**
     * Lists all FeedEntry entities by Author of a Topic.
     *
     * @Route("/topic/{id}/author", name="feedEntry_author")
     * @Template()
     */
    public function authorAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $topic = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$topic)
        {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $authors = $em->getRepository('PkrBuzzBundle:Author')->findByTopic($id);

        return array (
            'topic'   => $topic,
            'authors' => $authors
        );
    }

    /**
     * Lists all FeedEntry entities by Domain of a Topic.
     *
     * @Route("/topic/{id}/domain", name="feedEntry_domain")
     * @Template()
     */
    public function domainAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $topic = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$topic)
        {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $domains = $em->getRepository('PkrBuzzBundle:Domain')->findByTopic($id);

        return array (
            'topic'   => $topic,
            'domains' => $domains
        );
    }
}
