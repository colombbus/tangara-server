<?php

namespace Tangara\CoreBundle\Controller;


use Tangara\CoreBundle\Entity\Group;
use Symfony\Component\HttpFoundation\Response;



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
        
        $user_groups = $user->getGroups();
        $strangerGroups = groupsWithoutMe($groups, $user_groups);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:list.html.'.$this->getEngine(), array(
            'groups' => $groups, 
            'nogroups' => $strangerGroups));
    }
    
    public function newAction(\Symfony\Component\HttpFoundation\Request $request)
    {       
        $response = parent::newAction($request);
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $group = new Group();
        $group->setName("AdminGroup11");
        $group->addUser($user);
        
        return $response;
    }    
    
    /*
     * Give all informations about the group
     */
    public function infoGroupAction(Group $group)
    {       
        return $this->container->get('templating')->renderResponse('TangaraCoreBundle:Group:new.html.twig', array('group' => $group));
    }
    
    public function newAction(\Symfony\Component\HttpFoundation\Request $request) {
        //parent::newAction($request);
        
         /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
        $groupManager = $this->container->get('fos_user.group_manager');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.group.form.factory');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $group = $groupManager->createGroup('');
        
        

        $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::GROUP_CREATE_INITIALIZE, new \FOS\UserBundle\Event\GroupEvent($group, $request));

        $form = $formFactory->createForm();
        $form->setData($group);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new \FOS\UserBundle\Event\FormEvent($form, $request);
                $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::GROUP_CREATE_SUCCESS, $event);

                $groupManager->updateGroup($group);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_group_show', array('groupName' => $group->getName()));
                    $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
                }
                
                
                
                //recuper le groupe creer puis rajouter le groupLeader  
                $em = $this->container->get('doctrine.orm.entity_manager');
                $repository = $em->getRepository('TangaraCoreBundle:Group');
                $g = $repository->find($group->getId());
                
                $user = $this->container->get('security.context')->getToken()->getUser();
                
                $g->setGroupsLeader($user);
                $g->addUsers($user);
                $user->addGroupLeader($g);
                $user->addGroups($g);
                
                $em->persist($user);
                $em->persist($g);
                
                $em->flush();
                
                $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::GROUP_CREATE_COMPLETED, new \FOS\UserBundle\Event\FilterGroupResponseEvent($group, $request, $response));

                
                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:new.html.'.$this->getEngine(), array(
            'form' => $form->createview(),
        ));
        
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
