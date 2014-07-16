<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Group;
use Tangara\CoreBundle\Controller\ProjectController;

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
        return $this->container->get('templating')->renderResponse('TangaraCoreBundle:Group:user_group_show.html.twig', array('group' => $group));
    }
    
    //controleur vers la page de confirmation
    public function confirmationAction() {
        
        //recuperer le formulaire
        //...
        //formulaire le message
        $contenu_du_message = 'Demande de joindre le groupe...';
        
        //envoyer un mail
        $message = \Swift_Message::newInstance()
        ->setSubject('Demande de rejoidre le groupe')
        ->setFrom('test@example.com')
        ->setTo('group_leader@example.com')
        ->setBody($contenu_du_message)
        ;
        $this->container->get('mailer')->send($message);
         
       
        //return $this->render('TangaraCoreBundle:Project:confirmation.html.twig');
        return new Response('Message envoyé');
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
