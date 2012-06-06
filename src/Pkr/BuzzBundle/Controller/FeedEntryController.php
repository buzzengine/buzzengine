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
     * Lists all FeedEntry entities of a Topic.
     *
     * @Route(
     *  "/topic/{id}/{view}",
     *  name="feedEntry",
     *  requirements={"view" = "query|domain|author"},
     *  defaults={"view" = "query"}
     * )
     */
    public function indexAction($id, $view)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $topic = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$topic)
        {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        switch ($view)
        {
            case 'query':
                $viewScript = 'PkrBuzzBundle:FeedEntry:query.html.twig';
                break;
            case 'domain':
                $viewScript = 'PkrBuzzBundle:FeedEntry:domain.html.twig';
                break;
            case 'author':
                $viewScript = 'PkrBuzzBundle:FeedEntry:author.html.twig';
                break;
            default:
                throw $this->createNotFoundException('Unable to find view.');
        }

        $deleteForm = $this->_createDeleteForm($id);

        return $this->render($viewScript, array (
                'topic'       => $topic,
                'view'        => $view,
                'delete_form' => $deleteForm->createView()
        ));
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

        return $this->redirect($this->generateUrl('feedEntry', array ('id' => $id)));
    }

    protected function _createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                    ->add('id', 'hidden')
                    ->getForm();
    }
}
