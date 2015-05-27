<?php

/**
 * Description of AdminController
 */

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Form\Type\SearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends TangaraController {

    public function indexAction() {
        return $this->renderContent('TangaraCoreBundle:Admin:index.html.twig', 'profile', array());
    }

    public function usersAction(Request $request) {
        //TODO: check that user is admin
        $session = $request->getSession();
        $page = $session->get("admin_users_page", 1);
        $search = $session->get("admin_users_search", false);
        return $this->renderContent('TangaraCoreBundle:Admin:users.html.twig', 'profile', array('page' => $page, 'search' => $search));
    }
    
    public function getUsersAction(Request $request) {
        //TODO: check that user is admin
        if (!$request->isXmlHttpRequest()) {
            $this->redirect($this->generateUrl('tangara_admin_users'));
        }
        $session = $request->getSession();
        
        // handle page number
        $page = $request->get("page", 1);
        $session->set("admin_users_page", $page);           

        // handle search
        $search = $request->get("search", false);        
        if ($search !== false) {
            // search set: store it in session
            if (strlen(trim($search))==0) {
                // reset search
                $session->set("admin_users_search", false);           
            } else {
                $session->set("admin_users_search", $search);
            }
        }        
        // get search from session if any
        $search = $session->get("admin_users_search", false);
        
        // get users
        $repository = $this->getDoctrine()->getRepository('TangaraCoreBundle:User');
        
        if ($search !== false) {
            $users = $repository->getSearchQuery($search);
        } else {
            $users = $repository->findAll();
        }
        $pagination = $this->get('knp_paginator')->paginate($users, $page,10);
        return $this->render('TangaraCoreBundle:Admin:users_list.html.twig', array('users' => $pagination));
    }

    public function projectsAction() {
        $findProjects = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:Project')
                ->findAll();
        $projects = $this->get('knp_paginator')->paginate($findProjects, $this->get('request')->query->get('page', 1),10);
        return $this->renderContent('TangaraCoreBundle:Admin:projects.html.twig', 'profile', array('projects' => $projects));
    }

    public function projects_ajaxAction(){
        $form = $this->createForm(new SearchType());
        return $this->renderContent('TangaraCoreBundle:Admin:projects_ajax.html.twig', 'profile', array('form' => $form->createView()));
    }

    public function search_project_ajaxAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->get('search');
            $project = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('TangaraCoreBundle:Project')
                    ->searchProject($data);
//             \Doctrine\Common\Util\Debug::dump($project);
            $html = $this->renderView('TangaraCoreBundle:Admin:result_projects.html.twig', array('project' => $project));
            $response = new Response($html);
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        } else {
            $form = $this->createForm(new SearchType());
            return $this->renderContent('TangaraCoreBundle:Admin:projects_ajax.html.twig', 'profile', array('form' => $form->createView()));
        }
    }

}
