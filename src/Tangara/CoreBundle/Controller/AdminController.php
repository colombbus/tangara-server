<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller {

    public function indexAction() {
        return $this->render('TangaraCoreBundle:Admin:homepage.html.twig');
    }
    
    public function testAction() {
        return $this->render('TangaraCoreBundle:Admin:test.html.twig');
    }
}
