<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\TopicFeed;
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
     * Lists all Query entities of a Topic.
     *
     * @Route("/topic/{id}", name="query")
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

        $queries = $em->getRepository('PkrBuzzBundle:Query')->findByTopic($id);

        return array (
            'topic'   => $topic,
            'queries' => $queries
        );
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

        return array (
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView()
        );
    }

    /**
     * Displays a form to create a new Query entity.
     *
     * @Route("/new/topic/{id}", name="query_new")
     * @Template()
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $topic = $em->getRepository('PkrBuzzBundle:Topic')->find($id);

        if (!$topic)
        {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $query = new Query();
        $query->setTopic($topic);
        $form  = $this->createForm(new QueryType(), $query);

        return array (
            'entity' => $query,
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

            foreach ($entity->getTopic()->getCategories() as $category)
            {
                foreach ($category->getRawFeeds() as $rawFeed)
                {
                    $feed = new TopicFeed($rawFeed, $entity);
                    $em->persist($feed);
                }
            }

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

        if ($editForm->isValid())
        {
            foreach ($entity->getTopicFeeds() as $feed)
            {
                $feed->setDisabled($entity->getDisabled());
                $feed->generateUrl();
                $em->persist($feed);
            }

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

            foreach ($entity->getTopicFeeds() as $feed)
            {
                $feed->detachFromQuery();
                $feed->setDisabled(true);
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('query', array('id' => $entity->getTopic()->getId())));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
