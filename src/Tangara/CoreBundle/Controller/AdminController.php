<?php

/**
 * Description of AdminController
 */

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
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
        return $this->renderContent('TangaraCoreBundle:Admin:users.html.twig', 'profile', array('page' => $page, 'search' => $search, 'paginationRoute' => 'tangara_admin_users_list'));
    }
    
    public function getUsersAction(Request $request) {
        //TODO: check that user is admin
        if (!$request->isXmlHttpRequest()) {
            $this->redirect($this->generateUrl('tangara_admin_users'));
        }
        $pagination = $this->get('tangara_core.pagination')->paginate($request, "admin_users", 'TangaraCoreBundle:User');
        return $this->render('TangaraCoreBundle:Admin:users_list.html.twig', array('users' => $pagination));
    }

    public function projectsAction(Request $request) {
        //TODO: check that user is admin
        $session = $request->getSession();
        $page = $session->get("admin_projects_page", 1);
        $search = $session->get("admin_projects_search", false);
        return $this->renderContent('TangaraCoreBundle:Admin:projects.html.twig', 'profile', array('page' => $page, 'search' => $search, 'paginationRoute' => 'tangara_admin_projects_list'));
    }

    public function getProjectsAction(Request $request) {
        //TODO: check that user is admin
        if (!$request->isXmlHttpRequest()) {
            $this->redirect($this->generateUrl('tangara_admin_projects'));
        }
        $pagination = $this->get('tangara_core.pagination')->paginate($request, "admin_projects", 'TangaraCoreBundle:Project');
        return $this->render('TangaraCoreBundle:Admin:projects_list.html.twig', array('projects' => $pagination));
    }

}
