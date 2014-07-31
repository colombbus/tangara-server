<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller {

    public function indexAction() {
        return $this->render('TangaraCoreBundle:Admin:homepage.html.twig');
    }
    
    public function usersAction() {
        return $this->render('TangaraCoreBundle:Admin:users.html.twig');
    }
    
    
    
}
