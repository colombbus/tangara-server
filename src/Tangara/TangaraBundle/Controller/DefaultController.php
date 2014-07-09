<?php

namespace Tangara\TangaraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TangaraTangaraBundle:Default:homepage.html.twig');
    }
}
