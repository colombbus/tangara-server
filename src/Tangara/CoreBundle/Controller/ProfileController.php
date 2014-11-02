<?php

/*
 * Copyright (C) 2014 Régis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of ProfileController
 *
 * @author Régis
 */

namespace Tangara\CoreBundle\Controller;


use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Group;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends TangaraController
{

    /**
     * To get the user main page
     * @return type
     */
    public function profileAction($user_id) {
        $user = null;
        $edition = false;
        if ($user_id === false) {
            // get user id from logged user
            if (!$this->isUserLogged()) {
                return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
            } else {
                $user = $this->getUser();
                $edition = true;
            }
        } else {
            $user = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:User')
                ->findOneById($user_id);
            if (!$user) {
                return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
            }
            if ($user === $this->getUser()) {
                $edition = true;
            }
        }
        
        //$response = parent::profileAction();
        //$user = parent::showAction();
        return $this->renderContent('TangaraCoreBundle:Profile:show.html.twig', 'profile', array('user'=>$user, 'edition'=>$edition));
    }
    /**
     * remove user account
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delAccountAction(){
        $user = $this->getUser();

        // Delete home project
        $em = $this->container->get('doctrine.orm.entity_manager');
        $home = $user->getHome();
        if ($home) {
            $em->remove($home);
            $em->flush();
        }
        
        // Delete user
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->deleteUser($user);
        
        return new Response('Your account has been deleted.');
    }
    
    /**
     * Add a new user in join request list 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function askJoinGroupAction(){
        $user = $this->container->get('security.context')->getToken()->getUser();

        $msg = $this->container->get('request')->get('object');
        $goupId = $this->container->get('request')->get('groups');

        $em = $this->container->get('doctrine.orm.entity_manager');
        $groupRepository = $em->getRepository('TangaraCoreBundle:Group');
        $group = $groupRepository->find($goupId);
               
        
        //get leader group e-mail
        $groupLeader = $group->getGroupsLeader();     
        $leader_mail = $groupLeader->getEmail();

        if($groupRepository->isUserAsked($user->getUserName()) == null){
            //add the user in join request list
            $group->addJoinRequest($user);
            $em->persist($group);
            $em->flush();
            
            
            //send e-mail to the group leader 
            $message = \Swift_Message::newInstance()
                    ->setSubject('Demande de rejoidre le groupe')
                    ->setFrom('tangaraui@colombbus.org')
                    ->setTo('tangaraui@colombbus.org') //change with the group leader e-mail, $leader_mail
                    ->setBody($msg)
            ;
            $this->container->get('mailer')->send($message);


            //return $this->render('TangaraCoreBundle:Project:confirmation.html.twig');
            return new Response('Message sent.');
        }
        else{
            //return $this->render('TangaraCoreBundle:Project:confirmation.html.twig');
            return new Response('You already made a request to join this group.');
        }
    }
    
    public function logoutAction() {
        $request = $this->getRequest();
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {
            $jsonResponse = new JsonResponse();
            $content = $this->renderView('TangaraCoreBundle:User:anonymous.html.twig');            
            return $jsonResponse->setData(array('success' => true, 'content'=>$content));
        } else {
            // redirect the user to where they were before the login process begun.
            return $this->redirect($request->headers->get('referer'));
        }
    }
    
    public function menuAction() {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            // should never occur
            return $this->redirect($this->generateUrl( 'tangara_core_homepage' ));
        } else {
            // direct access
            return $this->render('TangaraCoreBundle:User:menu.html.twig', array('project'=>$this->getProject()));
        }
    }
    
    public function registerAction() {
        if ($this->isUserLogged()) {
            // If user is logged: redirect to main page
            return $this->redirect($this->generateUrl( 'tangara_core_homepage' ));
        }
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('POST')) {
                $response = $this->forward("FOSUserBundle:Registration:register");
                if ($response instanceof RedirectResponse) {
                    // redirect
                    return $this->redirect($response->getTargetUrl());
                } else {
                    $jsonResponse = new JsonResponse();
                    return $jsonResponse->setData(array('content'=>$response->getContent()));
                }
            } else {
                return $this->forward("FOSUserBundle:Registration:register");
            }
        } else {
            return $this->renderContent("TangaraCoreBundle:Profile:init.html.twig", 'profile', array('route'=>'tangara_user_register'));
        }
    }
    
    public function registrationConfirmedAction() {
        return $this->renderContent("TangaraCoreBundle:Profile:registration.confirmed.html.twig", 'profile', array('user'=>$this->getUser()));
    }

    public function changePasswordAction() {
        if (!$this->isUserLogged()) {
            // If user is not logged: redirect to main page
            return $this->redirect($this->generateUrl( 'tangara_core_homepage' ));
        }
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('POST')) {
                $response = $this->forward("FOSUserBundle:ChangePassword:changePassword");
                if ($response instanceof RedirectResponse) {
                    // redirect
                    return $this->redirect($response->getTargetUrl());
                } else {
                    $jsonResponse = new JsonResponse();
                    return $jsonResponse->setData(array('content'=>$response->getContent()));
                }
            } else {
                return $this->forward("FOSUserBundle:ChangePassword:changePassword");
            }
        } else {
            return $this->renderContent("TangaraCoreBundle:Profile:init.html.twig", 'profile', array('route'=>'tangara_user_change_password'));
        }
    }

    
    }
