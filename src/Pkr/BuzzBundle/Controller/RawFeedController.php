<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\TopicFeed;
use Pkr\BuzzBundle\Entity\RawFeed;
use Pkr\BuzzBundle\Form\RawFeedType;

/**
 * RawFeed controller.
 *
 * @Route("/raw-feed")
 */
class RawFeedController extends Controller
{
    /**
     * Lists all RawFeed entities.
     *
     * @Route("/category/{id}", name="rawfeed")
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

        $rawFeeds = $em->getRepository('PkrBuzzBundle:RawFeed')->findByCategory($id);

        return array (
            'category' => $category,
            'rawFeeds' => $rawFeeds
        );
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

        return array (
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView()
        );
    }

    /**
     * Displays a form to create a new RawFeed entity.
     *
     * @Route("/new/category/{id}", name="rawfeed_new")
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

        $rawFeed = new RawFeed();
        $rawFeed->setCategory($category);
        $form   = $this->createForm(new RawFeedType(), $rawFeed);

        return array (
            'entity' => $rawFeed,
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

            foreach ($entity->getCategory()->getTopics() as $topic)
            {
                foreach ($topic->getQueries() as $query)
                {
                    $feed = new TopicFeed($entity, $query);
                    $em->persist($feed);
                }
            }

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

        if ($editForm->isValid())
        {
            foreach ($entity->getTopicFeeds() as $feed)
            {
                $feed->generateUrl();
                $em->persist($feed);
            }

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

            foreach ($entity->getTopicFeeds() as $feed)
            {
                $feed->detachFromRawFeed();
                $feed->setDisabled(true);
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('rawfeed', array('id' => $entity->getCategory()->getId())));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
