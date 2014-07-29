<?php

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Entity\Group;
use Tangara\CoreBundle\Entity\GroupRepository;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GroupController extends Controller
{
    
    /**
     * Show all groups
     */
    public function listAction() {
       
        $user = $this->get('security.context')->getToken()->getUser();
    
        //on recupere tous les groupes
        $em = $this->getDoctrine()->getManager();
        $groupRepository = $em->getRepository('TangaraCoreBundle:Group');
        $groups = $groupRepository->findAll();
        

        //les groupes dont l'user est membre
        $user_groups = $user->getGroups();
        $g = new Group();
        $strangerGroups = $g->groupsWithoutMe($groups, $user_groups);
        
        return $this->render('TangaraCoreBundle:Group:list.html.twig', array(
            'groups' => $groups,
            'nogroups' => $strangerGroups));
    }

    /*
     * Give all informations about the group
     */
    public function infoGroupAction(Group $group) {
        $isProjects = $group->isProjects();

        return $this->render('TangaraCoreBundle:Group:show.html.twig', array('group' => $group, 'isProjects' => $isProjects));
    }

    //a changer le contenu
    public function newAction(\Symfony\Component\HttpFoundation\Request $request) {
        
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
                    $url = $this->container->get('router')->generate('tangara_user_group_info', array('id' => $group->getId()));
                    $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
                }
                
                // get group created then add groupLeader  
                $em = $this->container->get('doctrine.orm.entity_manager');
                $repository = $em->getRepository('TangaraCoreBundle:Group');
                $g = $repository->find($group->getId());

                $user = $this->container->get('security.context')->getToken()->getUser();

                $g->setGroupsLeader($user);
                $g->addUsers($user);
                
                $user->addRole('ROLE_ADMIN');
                $user->addGroupLeader($g);
                $user->addGroups($g);

                $em->persist($user);
                $em->persist($g);

                $em->flush();

                $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::GROUP_CREATE_COMPLETED, new \FOS\UserBundle\Event\FilterGroupResponseEvent($group, $request, $response));
                
                return $response;
            }
        }
    }

    //a changer le contenu
    public function editAction(\Symfony\Component\HttpFoundation\Request $request, $groupName) {
        //parent::editAction($request, $groupName);

        $group = $this->findGroupBy('name', $groupName);

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new \FOS\UserBundle\Event\GetResponseGroupEvent($group, $request);
        $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::GROUP_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.group.form.factory');

        $form = $formFactory->createForm();
        $form->setData($group);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
                $groupManager = $this->container->get('fos_user.group_manager');

                $event = new \FOS\UserBundle\Event\FormEvent($form, $request);
                $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::GROUP_EDIT_SUCCESS, $event);

                $groupManager->updateGroup($group);

                $isProjects = $group->isProjects();

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('tangara_user_groupInfo', array('groupName' => $group->getName(), 'isProjects' => $isProjects, 'id' => $group->getId()));
                    $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
                }

                $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::GROUP_EDIT_COMPLETED, new \FOS\UserBundle\Event\FilterGroupResponseEvent($group, $request, $response));

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:edit.html.' . $this->getEngine(), array(
                    'form' => $form->createview(),
                    'group_name' => $group->getName(),
        ));
    }

    public function deleteAction(\Symfony\Component\HttpFoundation\Request $request, $groupName) {
        parent::deleteAction($request, $groupName);
    }
    
    
    public function joinRequestAction(Group $group){
        
        $usersRequest = $group->getJoinRequest();
        
        return $this->container->get('templating')->renderResponse('TangaraCoreBundle:Group:group_leader.html.twig', array('usersRequest' => $usersRequest, "group" => $group));
    }
    
   
    public function acceptRequestAction() {
        
        $group_id = $this->container->get('request')->get('groupid');
        $user_id = $this->container->get('request')->get('valeur');

        $em = $this->container->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('TangaraCoreBundle:Group');
        $group = $repository->find($group_id);
        $repositoryU = $em->getRepository('TangaraCoreBundle:User');
        $user = $repositoryU->find($user_id);
  
        //$group->addUser($user);
        $user->addGroup($group);
        $group->removeJoinRequest($user);
        
        $em->persist($group);
        $em->persist($user);
        $em->flush();
        
        
        echo "Accepter l'user = ".$user->getUserName()." ".$group->getName();

        return new Response(NULL); 
        
    }
    
    public function refuseRequestAction() {
        
        $group_id = $this->container->get('request')->get('groupid');
        $user_id = $this->container->get('request')->get('valeur');
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('TangaraCoreBundle:Group');
        $group = $repository->find($group_id);
        $repositoryU = $em->getRepository('TangaraCoreBundle:User');
        $user = $repositoryU->find($user_id);
        
        $group->removeJoinRequest($user);
        
        $em->persist($group);
        $em->flush();
        
        echo "Refuser l'user id = ".$user_id;

        return new Response(NULL);        
    }
    

    public function delUserAction(){
        
        $group_id = $this->container->get('request')->get('groupid');
        $user_id = $this->container->get('request')->get('userid');
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('TangaraCoreBundle:Group');
        $group = $repository->find($group_id);
        $repositoryU = $em->getRepository('TangaraCoreBundle:User');
        $user = $repositoryU->find($user_id);
        
        
        $group->removeUsers($user);
        $user->removeGroups($group);
        $em->flush();
     
        echo 'Un user a ete supprime du group.';
 
        return new Response(NULL);      
    }
    
    
    
    
    public function delUserAction(){
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('TangaraCoreBundle:Group');
        $group = $repository->find($_GET['groupid']);
        $repositoryU = $em->getRepository('TangaraCoreBundle:User');
        $user = $repositoryU->find($_GET['userid']);
        
        
        $group->removeUsers($user);
        $user->removeGroups($group);
        $em->flush();
        
        
        
        
        
        echo 'Un user a ete supprime du group.';
        
        
        return new Response(NULL);
        
    }

        
}
