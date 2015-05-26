<?php

/**
 * Description of ProfileController
 * @author badr
 */

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Form\Type\SearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends TangaraController {

    public $val;
    public $user;

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
            $user = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('TangaraCoreBundle:User')
                    ->searchData($form['search']->getData());
            $this->val = $user;
            $session = $request->getSession()->set('val', $this->val);
        } elseif ($request->isMethod('GET')) {
            $user = $request->getSession()->get('val', $this->val);
        } else {
            throw $this->createNotFoundException('404 Not found');
        }
        $users = $this->get('knp_paginator')->paginate($user, $this->get('request')->query->get('page', 1), 2);
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



//
//    public function autoshowAction() {
//        $form = $this->createForm(new SearchType());
//        return $this->renderContent('TangaraCoreBundle:Admin:autocomplete.html.twig', 'profile', array('form' => $form->createView()));
//    }
//
//    public function autocompleteAction() {
//        $request = $this->get('request');
//        if ($request->isXmlHttpRequest()) {
//            $data = $request->request->get('user');
//            $user = $this->getDoctrine()
//                    ->getEntityManager()
//                    ->getRepository('TangaraCoreBundle:User')
//                    ->autocompleteData($data);
//            $response = new Response(json_encode($user));
//            $response->headers->set('Content-Type', 'application/json');
//            return $response;
//        }
//    }

}
