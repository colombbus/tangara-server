<?php

namespace Tangara\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

use Tangara\ProjectBundle\Controller\DefaultController as BaseController2;

use FOS\UserBundle\Controller\GroupController as BaseController;


class GroupController extends BaseController
{
     /**
     * Show all groups
     */
    public function listAction()
    {
        $groups = $this->container->get('fos_user.group_manager')->findGroups();
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        
        //$em = $this->getDoctrine()->getManager();
         
        //$repository_group = $em->getRepository('TangaraUserBundle:Group');
        //$allgroups = $repository_group->findAll();
        
        $user_groups = $user->getGroups();
        $tmp = allNoGroup($groups, $user_groups);
        
        //echo 'lol lol';

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:list.html.'.$this->getEngine(), array('groups' => $groups, 'nogroups' => $tmp));
    }
    
}

//return la liste des groupes dont l'user n'est pas membre
function allNoGroup($allgroups, $user_groups) {

    foreach ($allgroups as $key) {
        $dif = true;
        foreach ($user_groups as $key2) {
            if ($key->getId() == $key2->getId()) {
                $dif = false;
                break;
            }
        }
        if ($dif == true) {
            $tmp[] = $key;
        }
    }

    return $tmp;
}
