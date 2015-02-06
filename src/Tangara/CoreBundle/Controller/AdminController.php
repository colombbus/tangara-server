<?php
namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\User;

class AdminController extends TangaraController {
    
    public function indexAction(){
        
        $users = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:User')
                ->findAll();
        
        $projects = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:Project')
                ->findAll();
        
        return $this->renderContent('TangaraCoreBundle:Admin:index.html.twig', 'profile', array('users'=> $users, 'projects'=> $projects));
    }
    
}
