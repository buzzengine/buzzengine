<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\Query;
use Pkr\BuzzBundle\Form\QueryType;

/**
 * Query controller.
 *
 * @Route("/query")
 */
class QueryController extends Controller
{
    /**
     * Lists all Query entities.
     *
     * @Route("/", name="query")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PkrBuzzBundle:Query')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Query entity.
     *
     * @Route("/{id}/show", name="query_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Query')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Query entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Query entity.
     *
     * @Route("/new", name="query_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Query();
        $form   = $this->createForm(new QueryType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Query entity.
     *
     * @Route("/create", name="query_create")
     * @Method("post")
     * @Template("PkrBuzzBundle:Query:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Query();
        $request = $this->getRequest();
        $form    = $this->createForm(new QueryType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('query_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Query entity.
     *
     * @Route("/{id}/edit", name="query_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Query')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Query entity.');
        }

        $editForm = $this->createForm(new QueryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Query entity.
     *
     * @Route("/{id}/update", name="query_update")
     * @Method("post")
     * @Template("PkrBuzzBundle:Query:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PkrBuzzBundle:Query')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Query entity.');
        }

        $editForm   = $this->createForm(new QueryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('query_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Query entity.
     *
     * @Route("/{id}/delete", name="query_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PkrBuzzBundle:Query')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Query entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('query'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
