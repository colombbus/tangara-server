<?php

namespace Tangara\TangaraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TangaraTangaraBundle:Default:index.html.twig', array('name' => $name));
    }
}
