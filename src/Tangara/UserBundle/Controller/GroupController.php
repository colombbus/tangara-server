<?php

namespace Tangara\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Tangara\UserBundle\Entity\Group;

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
        $strangerGroups = groupsWithoutMe($groups, $user_groups);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:list.html.'.$this->getEngine(), array(
            'groups' => $groups, 
            'nogroups' => $strangerGroups));
    }
    
    /*
     * Give all informations about the group
     */
    public function infoGroupAction(Group $group)
    {       
        return $this->container->get('templating')->renderResponse('TangaraUserBundle:Group:user_group_show.html.twig', array('group' => $group));
    }
    
}

/*
 * This function give all groups where i am not a member
 */
function groupsWithoutMe($allgroups, $user_groups) {

    foreach ($allgroups as $group) {
        $trigger = true;
        foreach ($user_groups as $user) {
            if ($group->getId() == $user->getId()) {
                $trigger = false;
                break;
            }
        }
        if ($trigger == true) {
            $groupsWithoutMe[] = $group;
        }
    }
    return $groupsWithoutMe;
}
