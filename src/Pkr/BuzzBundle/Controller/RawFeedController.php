<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\RawFeed;
use Pkr\BuzzBundle\Form\RawFeedType;

/**
 * RawFeed controller.
 *
 * @Route("/rawfeed")
 */
class RawFeedController extends Controller
{
    /**
     * Lists all RawFeed entities.
     *
     * @Route("/", name="rawfeed")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PkrBuzzBundle:RawFeed')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a RawFeed entity.
     *
     * @Route("/{id}/show", name="rawfeed_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:RawFeed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RawFeed entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new RawFeed entity.
     *
     * @Route("/new", name="rawfeed_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new RawFeed();
        $form   = $this->createForm(new RawFeedType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new RawFeed entity.
     *
     * @Route("/create", name="rawfeed_create")
     * @Method("post")
     * @Template("PkrBuzzBundle:RawFeed:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new RawFeed();
        $request = $this->getRequest();
        $form    = $this->createForm(new RawFeedType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rawfeed_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing RawFeed entity.
     *
     * @Route("/{id}/edit", name="rawfeed_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:RawFeed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RawFeed entity.');
        }

        $editForm = $this->createForm(new RawFeedType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing RawFeed entity.
     *
     * @Route("/{id}/update", name="rawfeed_update")
     * @Method("post")
     * @Template("PkrBuzzBundle:RawFeed:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:RawFeed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RawFeed entity.');
        }

        $editForm   = $this->createForm(new RawFeedType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rawfeed_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a RawFeed entity.
     *
     * @Route("/{id}/delete", name="rawfeed_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PkrBuzzBundle:RawFeed')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RawFeed entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('rawfeed'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
