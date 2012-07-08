<?php

namespace Pkr\BuzzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('topic'));
    }

    /**
     * @Route("/fetch/{id}", name="fetch", defaults={"id" = null})
     */
    public function fetchAction($id)
    {
        $feedService = $this->get('pkr_buzz.service.feed');
        $feedService->fetch($id);

        return $this->redirect($this->generateUrl('log'));
    }
}
