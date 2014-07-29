<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;



class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('TangaraCoreBundle:Default:homepage.html.twig');
    }

    public function ajaxAction(){
        return $this->render('TangaraCoreBundle:Default:ajax_symfony.html.twig');
    }
     
    public function translationAction(){
        $t = $this->get('translator')->trans('_logout');
        return new Response($t);
    }

}
