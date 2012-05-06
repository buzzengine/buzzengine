<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\Topic;
use Pkr\BuzzBundle\Form\TopicType;

/**
 * Topic controller.
 *
 * @Route("/topic")
 */
class TopicController extends Controller
{
    /**
     * Lists all Topic entities.
     *
     * @Route("/", name="topic")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PkrBuzzBundle:Topic')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Topic entity.
     *
     * @Route("/{id}/show", name="topic_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Topic entity.
     *
     * @Route("/new", name="topic_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Topic();
        $form   = $this->createForm(new TopicType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Topic entity.
     *
     * @Route("/create", name="topic_create")
     * @Method("post")
     * @Template("PkrBuzzBundle:Topic:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Topic();
        $request = $this->getRequest();
        $form    = $this->createForm(new TopicType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('topic_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Topic entity.
     *
     * @Route("/{id}/edit", name="topic_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $editForm = $this->createForm(new TopicType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Topic entity.
     *
     * @Route("/{id}/update", name="topic_update")
     * @Method("post")
     * @Template("PkrBuzzBundle:Topic:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $editForm   = $this->createForm(new TopicType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('topic_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Topic entity.
     *
     * @Route("/{id}/delete", name="topic_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Topic entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('topic'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
