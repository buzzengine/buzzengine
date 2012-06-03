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

        $deleteForm = $this->_createDeleteForm($id);
        $authors = $em->getRepository('PkrBuzzBundle:Author')->findByTopic($id);

        return array (
            'topic'       => $topic,
            'authors'     => $authors,
            'delete_form' => $deleteForm->createView()
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

        $deleteForm = $this->_createDeleteForm($id);
        $domains = $em->getRepository('PkrBuzzBundle:Domain')->findByTopic($id);

        return array (
            'topic'       => $topic,
            'domains'     => $domains,
            'delete_form' => $deleteForm->createView()
        );
    }

    /**
     * Deletes all Author, Domain and FeedEntry entities of a Topic.
     *
     * @Route("/topic/{id}/delete", name="feedEntry_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->_createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();

            $authors = $em->getRepository('PkrBuzzBundle:Author')->findByTopic($id);
            foreach ($authors as $author)
            {
                $em->remove($author);
            }

            $domains = $em->getRepository('PkrBuzzBundle:Domain')->findByTopic($id);
            foreach ($domains as $domain)
            {
                $em->remove($domain);
            }

            $em->flush();
        }

        return $this->redirect($this->generateUrl('feedEntry_author', array ('id' => $id)));
    }

    protected function _createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                    ->add('id', 'hidden')
                    ->getForm();
    }
}
