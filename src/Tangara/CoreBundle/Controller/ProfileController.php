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


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Group;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends Controller
{

    /**
     * To get the user main page
     * @return type
     */
    public function profileAction() {

        //$response = parent::profileAction();
        //$user = parent::showAction();
        return $this->render('TangaraCoreBundle:Profile:show.html.twig');
    }
    /**
     * remove user account
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delAccountAction(){  
        $user = $this->get('security.context')->getToken()->getUser();

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
            $referer_url = $request->headers->get('referer');
            $response = new RedirectResponse($referer_url);		
            return $response;
        }
    }    

}
