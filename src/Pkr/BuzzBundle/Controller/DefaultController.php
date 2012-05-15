<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/run")
     */
    public function runAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PkrBuzzBundle:Feed')->findBy(array('disabled' => false));

        foreach ($entities as $entity)
        {
            try
            {
                $feed = \Zend\Feed\Reader\Reader::import($entity->getUrl());
            }
            catch (Exception $e)
            {
                var_dump($e->getMessage());
                die(__FILE__ . ' - ' . __LINE__);
            }

            foreach ($feed as $entry)
            {
                echo 'getTitle: ' . $entry->getTitle() . '<br>';
            }
        }

        var_dump('run finished');
        die(__FILE__ . ' - ' . __LINE__);
    }
}
