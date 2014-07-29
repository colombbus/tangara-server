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


class ProfileController extends Controller
{
    //Controller to get the user main page
    public function profileAction() {

        //$response = parent::profileAction();
        //$user = parent::showAction();
        $user = $this->get('security.context')->getToken()->getUser();

        return $this->render('TangaraCoreBundle:Profile:show.html.twig',
                array('user' => $user));
    }

    //delete account
    public function delAccountAction(){
        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->container->get('doctrine.orm.entity_manager');

        // remove user projects
        $projects = $user->getProjects();
        foreach ($projects as $p){
            $em->remove($p);
        }
        // remove from groups joined
        $groups = $user->getGroups();
        foreach ($groups as $g){
            // remove groups and projects if user is Leader
            if($g->getGroupsLeader()->getId() == $user->getId()){
                $gProjects = $g->getProjects();
                foreach ($gProjects as $gp){
                    $em->remove($gp);
                }
                $em->remove($g);
            }
            $user->removeGroups($g);
        }
        //supprimer le compte user
        $em->remove($user);
        $em->flush();

        return new Response('Votre Compte a ete supprime');
    }

    public function askJoinGroupAction(){
        $user = $this->container->get('security.context')->getToken()->getUser();

        $msg = $this->container->get('request')->get('object');
        $goupId = $this->container->get('request')->get('groups');

        $em = $this->container->get('doctrine.orm.entity_manager');
        $groupRepository = $em->getRepository('TangaraCoreBundle:Group');
        $group = $groupRepository->find($goupId);

        //touver le leader du group, donc user puis som adresse email
        $groupLeader = $group->getGroupsLeader();
        $leader_mail = $groupLeader->getEmail();

        if($groupRepository->isUserAsked($user->getUserName()) == null){
            //ajouter l'user dans la liste des demandes
            $group->addJoinRequest($user);
            $em->persist($group);
            $em->flush();

            /*
              //envoyer un mail au leader
              $message = \Swift_Message::newInstance()
              ->setSubject('Demande de rejoidre le groupe')
              ->setFrom('tangaraui@colombbus.org')
              ->setTo('tangaraui@colombbus.org') //a changer avec le mail $leader_mail
              ->setBody($msg)
              ;
              $this->container->get('mailer')->send($message);
             */

            //return $this->render('TangaraCoreBundle:Project:confirmation.html.twig');
            return new Response('Message envoyé.');
        }
        else{
            //return $this->render('TangaraCoreBundle:Project:confirmation.html.twig');
            return new Response('Vous avez deja fait une demande pour rejoindre ce groupe.');
        }
    }

}
