<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\Feed;
use Pkr\BuzzBundle\Form\FeedType;

/**
 * Feed controller.
 *
 * @Route("/feed")
 */
class FeedController extends Controller
{
    /**
     * Lists all Feed entities.
     *
     * @Route("/category/{id}", name="feed")
     * @Template()
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $category = $em->getRepository('PkrBuzzBundle:Category')->find($id);

        if (!$category)
        {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $feeds = $em->getRepository('PkrBuzzBundle:Feed')->findByCategory($id);

        return array (
            'category' => $category,
            'feeds'    => $feeds
        );
    }

    /**
     * Displays a form to create a new Feed entity.
     *
     * @Route("/new/category/{id}", name="feed_new")
     * @Template()
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository('PkrBuzzBundle:Category')->find($id);

        if (!$category)
        {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $feed = new Feed();
        $feed->setCategory($category);
        $form = $this->createForm(new FeedType(), $feed);

        return array (
            'entity' => $feed,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Feed entity.
     *
     * @Route("/create", name="feed_create")
     * @Method("post")
     * @Template("PkrBuzzBundle:Feed:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Feed();
        $request = $this->getRequest();
        $form    = $this->createForm(new FeedType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('feed_show', array('id' => $entity->getId())));

        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Feed entity.
     *
     * @Route("/{id}/edit", name="feed_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Feed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feed entity.');
        }

        $editForm = $this->createForm(new FeedType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Feed entity.
     *
     * @Route("/{id}/update", name="feed_update")
     * @Method("post")
     * @Template("PkrBuzzBundle:Feed:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Feed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feed entity.');
        }

        $editForm   = $this->createForm(new FeedType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('feed_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Feed entity.
     *
     * @Route("/{id}/delete", name="feed_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PkrBuzzBundle:Feed')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feed entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('feed', array('id' => $entity->getCategory()->getId())));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
