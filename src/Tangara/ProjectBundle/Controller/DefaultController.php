<?php

namespace Tangara\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function showAction($name)
    {
        $m = $this->getDoctrine()->getManager();
                
        return $this->render('TangaraProjectBundle:Default:show.html.twig', array('name' => $name));
    }
    
}
