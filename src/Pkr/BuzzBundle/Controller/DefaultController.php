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
        $feedService = $this->get('pkr_buzz.service.feed');
        $feedService->run();

        var_dump('run finished');
        die(__FILE__ . ' - ' . __LINE__);
    }
}
