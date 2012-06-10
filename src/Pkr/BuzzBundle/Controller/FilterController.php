<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity;
use Pkr\BuzzBundle\Form;

/**
 * Filter controller.
 *
 * @Route("/filter")
 */
class FilterController extends Controller
{
    /**
     * Lists all filters of a Topic.
     *
     * @Route("/topic/{id}", name="filter")
     * @Template()
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $topic = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$topic)
        {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        return array (
            'topic' => $topic
        );
    }

    /**
     * Displays a form to create a new filter.
     *
     * @Route(
     *      "/new/topic/{id}/{filter}",
     *      name="filter_new",
     *      requirements={"filter" = "regex|languageDetectlanguageCom"}
     * )
     * @Template()
     */
    public function newAction($id, $filter)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $topic = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$topic)
        {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        switch ($filter)
        {
            case 'languageDetectlanguageCom':
                $entity = new Entity\FilterLanguageDetectlanguageCom();
                $formType = new Form\FilterLanguageDetectlanguageComType();
                break;
            default:
                throw $this->createNotFoundException('Unable to find filter.');
        }

        $entity->setTopic($topic);
        $form = $this->createForm($formType, $entity);

        return array (
            'filter' => $filter,
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new filter.
     *
     * @Route(
     *      "/create/{filter}",
     *      name="filter_create",
     *      requirements={"filter" = "regex|languageDetectlanguageCom"}
     * )
     * @Method("post")
     * @Template("PkrBuzzBundle:Filter:new.html.twig")
     */
    public function createAction($filter)
    {
        switch ($filter)
        {
            case 'languageDetectlanguageCom':
                $entity = new Entity\FilterLanguageDetectlanguageCom();
                $formType = new Form\FilterLanguageDetectlanguageComType();
                break;
            default:
                throw $this->createNotFoundException('Unable to find filter.');
        }

        $request = $this->getRequest();
        $form    = $this->createForm($formType, $entity);
        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl(
                    'filter',
                    array ('id' => $entity->getTopic()->getId())
            ));
        }

        return array (
            'filter' => $filter,
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing filter.
     *
     * @Route(
     *      "/{id}/edit/{filter}",
     *      name="filter_edit"),
     *      requirements={"filter" = "regex|languageDetectlanguageCom"}
     * @Template()
     */
    public function editAction($id, $filter)
    {
        $em = $this->getDoctrine()->getEntityManager();

        switch ($filter)
        {
            case 'languageDetectlanguageCom':
                $entity = $em->getRepository('PkrBuzzBundle:FilterLanguageDetectlanguageCom')
                             ->find($id);
                $formType = new Form\FilterLanguageDetectlanguageComType();
                break;
            default:
                throw $this->createNotFoundException('Unable to find filter.');
        }

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find filter.');
        }

        $editForm = $this->createForm($formType, $entity);
        $deleteForm = $this->_createDeleteForm($id);

        return array (
            'filter'      => $filter,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing filter.
     *
     * @Route(
     *      "/{id}/update/{filter}",
     *      name="filter_update"),
     *      requirements={"filter" = "regex|languageDetectlanguageCom"}
     * @Method("post")
     * @Template("PkrBuzzBundle:Filter:edit.html.twig")
     */
    public function updateAction($id, $filter)
    {
        $em = $this->getDoctrine()->getEntityManager();

        switch ($filter)
        {
            case 'languageDetectlanguageCom':
                $entity = $em->getRepository('PkrBuzzBundle:FilterLanguageDetectlanguageCom')
                             ->find($id);
                $formType = new Form\FilterLanguageDetectlanguageComType();
                break;
            default:
                throw $this->createNotFoundException('Unable to find filter.');
        }

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find filter.');
        }

        $editForm   = $this->createForm($formType, $entity);
        $deleteForm = $this->_createDeleteForm($id);

        $request = $this->getRequest();
        $editForm->bindRequest($request);

        if ($editForm->isValid())
        {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl(
                    'filter',
                    array('id' => $entity->getTopic()->getId())
            ));
        }

        return array (
            'filter'      => $filter,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a filter.
     *
     * @Route(
     *      "/{id}/delete/{filter}",
     *      name="filter_delete",
     *      requirements={"filter" = "regex|languageDetectlanguageCom"}
     * )
     * @Method("post")
     */
    public function deleteAction($id, $filter)
    {
        $form = $this->_createDeleteForm($id);
        $request = $this->getRequest();
        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();

            switch ($filter)
            {
                case 'languageDetectlanguageCom':
                    $entity = $em->getRepository('PkrBuzzBundle:FilterLanguageDetectlanguageCom')
                                 ->find($id);
                    break;
                default:
                    throw $this->createNotFoundException('Unable to find filter.');
            }

            if (!$entity)
            {
                throw $this->createNotFoundException('Unable to find filter.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl(
                'filter',
                array ('id' => $entity->getTopic()->getId())
        ));
    }

    protected function _createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
