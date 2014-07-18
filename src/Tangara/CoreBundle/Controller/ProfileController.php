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

use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Group;
use Tangara\CoreBundle\Entity\User;

class ProfileController extends BaseController {

    //Controller to get the user main page
    public function profileAction() {

        //$response = parent::profileAction();
        //$user = parent::showAction();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $re = new Group();
        $re->setName("AdminGroup9");
        $re->addUser($user);
        
        $this->container->get('session')->getFlashBag()->add(
            'notice',
            'Vos changements ont été sauvegardés!'
        );
                
        return $this->container->get('templating')->renderResponse('TangaraCoreBundle:Profile:show.html.' . $this->container->getParameter('fos_user.template.engine'), 
                array('user' => $user));
    }
    
    //delete account
    public function delAccountAction(){  
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        $repositoryGroup = $em->getRepository('TangaraCoreBundle:Group');
             
        //supprimer les projets de l'user
        $projects = $user->getProjects();
        foreach ($projects as $p){
            $em->remove($p);
        }
               
        //supprimer dans la liste des groups aux quels il appartient  
        $groups = $user->getGroups();
        foreach ($groups as $g){
            //supprimer le goupe si il est le Leader et les projets du group
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
        //$em->remove($user);
        $em->flush();
        
        return new Response('Votre Compte a ete supprimer');
        
    }

}
