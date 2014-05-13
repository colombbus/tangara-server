<?php

namespace Tangara\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TangaraUserBundle:Default:index.html.twig');
    }
}
