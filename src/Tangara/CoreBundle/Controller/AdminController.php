<?php
/**
 * Description of ProfileController
 *
 * @author badr
 */
namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Form\Type\SearchType;

class AdminController extends TangaraController {
    
    public $val;
    
    public function indexAction(){     
        return $this->renderContent('TangaraCoreBundle:Admin:index.html.twig', 'profile', array());
    }
    
    public function usersAction(){
        $findUsers = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:User')
                ->findAll();        
        $users  = $this->get('knp_paginator')->paginate($findUsers, $this->get('request')->query->get('page', 1), 4);
        return $this->renderContent('TangaraCoreBundle:Admin:users.html.twig', 'profile', array('users'=> $users));
    }
    
    public function projectsAction(){
        $findProjects = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:Project')
                ->findAll();
        $projects  = $this->get('knp_paginator')->paginate($findProjects, $this->get('request')->query->get('page', 1), 5);
        return $this->renderContent('TangaraCoreBundle:Admin:projects.html.twig', 'profile', array('projects'=> $projects));
    }
    
    public function searchAction(){
        $form = $this->createForm(new SearchType());
        return $this->render('TangaraCoreBundle:Admin:search.html.twig', array('form'=> $form->createView()));
    }
       
    public function searchUserAction(){
        $form = $this->createForm(new SearchType());
        $request = $this->getRequest(); 
       if ($request->isMethod('POST')) {
            $form->bind($this->get('request'));
            $user = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:User')
                ->searchData($form['search']->getData());
            $this->val = $user;
            $session = $request->getSession()->set('val', $this->val);
        } 
        elseif ($request->isMethod('GET')) {
            $user = $request->getSession()->get('val', $this->val);
        }
        else {
            throw $this->createNotFoundException('404 Not found');
        }
        $users  = $this->get('knp_paginator')->paginate($user, $this->get('request')->query->get('page', 1), 2);
        return $this->renderContent('TangaraCoreBundle:Admin:users.html.twig', 'profile', array('users'=> $users));
    }
}
