<?php

/**
 * Description of AdminController
 */

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Form\Type\SearchType;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends TangaraController {

    public function indexAction() {
        return $this->renderContent('TangaraCoreBundle:Admin:index.html.twig', 'profile', array());
    }

    public function usersAction() {
        $findUsers = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:User')
                ->findAll();
        $users = $this->get('knp_paginator')->paginate($findUsers, $this->get('request')->query->get('page', 1), 4);
        return $this->renderContent('TangaraCoreBundle:Admin:users.html.twig', 'profile', array('users' => $users));
    }

    public function projectsAction() {
        $findProjects = $this->getDoctrine()
                ->getRepository('TangaraCoreBundle:Project')
                ->findAll();
        $projects = $this->get('knp_paginator')->paginate($findProjects, $this->get('request')->query->get('page', 1), 5);
        return $this->renderContent('TangaraCoreBundle:Admin:projects.html.twig', 'profile', array('projects' => $projects));
    }

    public function searchAction() {
        $form = $this->createForm(new SearchType());
        return $this->render('TangaraCoreBundle:Admin:search.html.twig', array('form' => $form->createView()));
    }

    public function searchUserAction() {
        $form = $this->createForm(new SearchType());
        $request = $this->getRequest();
        if ($request->isMethod('POST')) {
            $form->bind($this->get('request'));
            $searchedUsers = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('TangaraCoreBundle:User')
                    ->searchData($form['search']->getData());
            $request->getSession()->set('searchedUsers', $searchedUsers);
        } elseif ($request->isMethod('GET')) {
            $searchedUsers = $request->getSession()->get('searchedUsers');
        } else {
            throw $this->createNotFoundException('404 Not found');
        }
        $users = $this->get('knp_paginator')->paginate($searchedUsers, $this->get('request')->query->get('page', 1), 2);
        return $this->renderContent('TangaraCoreBundle:Admin:users.html.twig', 'profile', array('users' => $users));
    }

    public function users_ajaxAction() {
        $form = $this->createForm(new SearchType());
        return $this->renderContent('TangaraCoreBundle:Admin:users_ajax.html.twig', 'profile', array('form' => $form->createView()));
    }

    public function search_ajaxAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->get('search');
            $user = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('TangaraCoreBundle:User')
                    ->searchData($data);
            $html = $this->renderView('TangaraCoreBundle:Admin:result_users.html.twig', array('user' => $user));
            $response = new Response($html);
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        } else {
            $form = $this->createForm(new SearchType());
            return $this->renderContent('TangaraCoreBundle:Admin:users_ajax.html.twig', 'profile', array('form' => $form->createView()));
        }
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
