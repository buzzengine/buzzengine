<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pkr\BuzzBundle\Entity\Log;

/**
 * Log controller.
 *
 * @Route("/log")
 */
class LogController extends Controller
{
    /**
     * Lists all Log entities.
     *
     * @Route("/", name="log")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PkrBuzzBundle:Log')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Deletes all Log entities.
     *
     * @Route("/delete", name="log_delete")
     * @Method("post")
     */
    public function deleteAction()
    {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();
        $query = $connection->getDatabasePlatform()->getTruncateTableSql('Log');
        $connection->exec($query);

        return $this->redirect($this->generateUrl('log'));
    }
}
