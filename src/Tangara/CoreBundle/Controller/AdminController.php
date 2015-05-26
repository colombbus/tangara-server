<?php
/**
 * Description of ProfileController
 *
 * @author badr
 */

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\User;

class AdminController extends TangaraController {
    
    public function indexAction(){     
        return $this->renderContent('TangaraCoreBundle:Admin:index.html.twig', 'profile', array());
    }
    
    public function usersAction(){
        $findUsers = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:User')
                ->findAll();        
        $users  = $this->get('knp_paginator')->paginate($findUsers, $this->get('request')->query->get('page', 1), 3);
        return $this->renderContent('TangaraCoreBundle:Admin:users.html.twig', 'profile', array('users'=> $users));
    }
    
    public function projectsAction(){
        $findProjects = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:Project')
                ->findAll();
        $projects  = $this->get('knp_paginator')->paginate($findProjects, $this->get('request')->query->get('page', 1), 5);
        return $this->renderContent('TangaraCoreBundle:Admin:projects.html.twig', 'profile', array('projects'=> $projects));
    }
    
}
