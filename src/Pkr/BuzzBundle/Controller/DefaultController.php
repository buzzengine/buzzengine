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
     * @Route("/fetch/{id}", name="fetch", defaults={"id" = null})
     */
    public function fetchAction($id)
    {
        $feedService = $this->get('pkr_buzz.service.feed');
        $feedService->fetch($id);

        if (is_null($id))
        {
            $redirect = $this->generateUrl('topic');
        }
        else
        {
            $redirect = $this->generateUrl('topic_show', array ('id' => $id));
        }

        # return $this->redirect($redirect);

        var_dump('fetch finished');
        die(__FILE__ . ' - ' . __LINE__);
    }
}
