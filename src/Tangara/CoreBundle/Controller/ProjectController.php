<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Form\Type\SearchType;
use Tangara\CoreBundle\Entity\File;
use Tangara\CoreBundle\Entity\FileRepository;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\User;
use Tangara\CoreBundle\Form\Type\ProjectType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class ProjectController extends TangaraController {

    public function showAction($projectId) {
        $params = array();
        // Check if project id set
        $request = $this->getRequest();
        $session = $request->getSession();
        if ($projectId === false) {
            $projectId = $session->get('projectid');
        }
        // get manager
        $manager = $this->get('tangara_core.project_manager');

        if (!$projectId) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        // Check if project exists
        $project = $manager->getRepository()->find($projectId);
        if (!$project) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        $params['project'] = $project;

        // Check user
        $edition = false;
        $owner = false;
        $admin = false;
        $selectable = false;
        $access = false;

        //TODO: use ACL
        if ($this->isUserLogged()) {
            $user = $this->getUser();
            if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $admin = true;
                $access = true;
                $edition = true;
                $selectable = true;
            } else {
                if ($manager->isAuthorized($project, $user)) {
                    $selectable = true;
                    $access = true;
                }
                if ($project->getOwner() === $user) {
                    $edition = true;
                    $owner = true;
                }
            }
            if ($manager->isHomeProject($project, $user)) {
                $params['message'] = "project.home_project";
            }
        }
//        
//        if (!$access) {
//            // Check that project is public
//            if (!$project->getPublished()) {
//                // User not authorized
//                return $this->redirect($this->generateUrl('tangara_core_homepage'));
//            }
//        }

        $params['edition'] = $edition;
        $params['owner'] = $owner;
        $params['admin'] = $admin;
        $params['selectable'] = $selectable;
        if ($session->get('userMenuUpdateRequired')) {
            $session->remove('userMenuUpdateRequired');
            $params['updateUserMenu'] = true;
        }
        return $this->renderContent('TangaraCoreBundle:Project:show.html.twig', 'project', $params);
    }

    public function editAction($projectId) {
        // Check if project id set
        $request = $this->getRequest();
        $session = $request->getSession();
        // get manager
        $manager = $this->get('tangara_core.project_manager');

        if (!$this->isUserLogged()) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        if ($projectId === false) {
            $projectId = $session->get('projectid');
        }

        if (!$projectId) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        // Check if project exists
        $project = $manager->getRepository()->find($projectId);
        if (!$project) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        // Check user
        $user = $this->getUser();

        // Check project access by user
        //TODO: user ACL

        $securityContext = $this->get('security.context');

        //$auth = $this->get('security.context')->isGranted('ROLE_ADMIN')||$project->getOwner() == $user;
        $auth = $securityContext->isGranted('EDIT', $project);
        if (false === $auth && false === $securityContext->isGranted('ROLE_ADMIN')) {
            // User not authorized
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        $form = $this->createForm('project', $project);

        if ($request->isMethod('POST')) {
            // form submitted
            $form->handleRequest($request);

            if ($form->isValid()) {
                $manager->saveProject($project);
                if ($project->getId() == $session->get('projectid')) {
                    // current project has been updated: user menu has to be updated
                    $session->set('userMenuUpdateRequired', true);
                }
                $session->getFlashBag()->add('success', $this->get('translator')->trans('project.edited'));
                return $this->redirect($this->generateUrl('tangara_project_show', array('projectId' => $project->getId())));
            }
        }
        if (isset($form->attr)) {
            $form->attr = array_merge($form->attr, array('class' => 'form-content'));
        } else {
            $form->attr = array('class' => 'form-content');
        }
        return $this->renderContent('TangaraCoreBundle:Project:edit.html.twig', 'project', array('form' => $form->createView()));
    }

    public function publishedAction() {
        $findProjects = $this->get('tangara_core.project_manager')->getRepository()->getPublishedProjects();
        $projects = $this->get('knp_paginator')->paginate($findProjects, $this->get('request')->query->get('page', 1), 6);
        return $this->renderContent('TangaraCoreBundle:Project:published.html.twig', 'discover', array('projects' => $projects));
    }

    public function executeAction($projectId) {
        $request = $this->getRequest();
        $session = $request->getSession();
        $params = array();
        // get manager
        $manager = $this->get('tangara_core.project_manager');

        if ($projectId === false) {
            // try to get project from session
            $projectId = $session->get('projectid');
        }

        if (!$projectId) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        // Check if project exists
        $project = $manager->getRepository()->find($projectId);
        if (!$project) {
            // no current project: we should not be here
        }
        // check that launcher is set and exists
        $launcher = $project->getLauncher();
        if (!isset($launcher)) {
            //TODO: check that file actually exists
            $params['error'] = 'project.launcher_not_set';
        }

        $params['project'] = $project;

        $width = $project->getWidth();
        if (isset($width) && $width > 0) {
            $params['width'] = $width;
        }

        $height = $project->getHeight();
        if (isset($height) && $height > 0) {
            $params['height'] = $height;
        }

        $securityContext = $this->get('security.context');

        $access = false;
        if ($this->isUserLogged()) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            //$auth = $manager->isAuthorized($project, $user);
            $auth = $securityContext->isGranted('EDIT', $project);

            if (false === $auth && false === $securityContext->isGranted('ROLE_ADMIN')) {
                $access = false;
                return $this->redirect($this->generateUrl('tangara_core_homepage'));
            }
        }

        if (!$access) {
            // Check that project is public
            if (!$project->getPublished()) {
                // User not authorized
                return $this->redirect($this->generateUrl('tangara_core_homepage'));
            }
        }


        $tangarajs = $this->generateUrl('tangara_core_homepage') . $this->container->getParameter('tangara_core.settings.directory.tangarajs') . "/execute.html";
        $params['tangarajs'] = $tangarajs;
        return $this->render('TangaraCoreBundle:Project:execute.html.twig', $params);
    }

    public function listAction() {
        if (!$this->isUserLogged()) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $repository = $this->get('tangara_core.project_manager')->getRepository();
        $user = $this->getUser();
        $params = array();
        $params['ownProjects'] = $repository->getOwnedProjects($user);
        $params['readonlyProjects'] = $repository->getReadOnlyProjects($user);
        return $this->renderContent('TangaraCoreBundle:Project:list.html.twig', 'project', $params);
    }

    public function selectAction($projectId) {
        // get manager
        $manager = $this->get('tangara_core.project_manager');
        $project = $manager->getRepository()->find($projectId);
        $user = $this->getUser();

        if (!$this->isUserLogged()) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $auth = $this->get('security.context')->isGranted('ROLE_ADMIN') || $manager->isAuthorized($project, $user);
        if (!$auth) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $this->getRequest()->getSession()->set('projectid', $projectId);
        return $this->renderContent('TangaraCoreBundle:Project:select.html.twig', 'create');
    }

    //action for create a news prject
    public function createAction(Request $request) {
        $manager = $this->get('tangara_core.project_manager');
        if (!$this->isUserLogged()) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        } else {
            $project = new Project();
            $form = $this->createFormBuilder($project)
                    ->add('name', 'text', array('label' => 'project.name', 'required' => false))
                    ->add('published', 'checkbox', array('label' => 'project.published', 'required' => false))
                    ->add('width', 'integer', array('label' => 'project.width', 'required' => false))
                    ->add('height', 'integer', array('label' => 'project.height', 'required' => false))
                    ->add('description', 'textarea', array('label' => 'project.description', 'required' => false))
                    ->add('instructions', 'textarea', array('label' => 'project.instructions', 'required' => false))
                    ->add('save', 'submit', array('label' => 'project.save', 'attr' => array('several-buttons', 'last-button')))
                    ->getForm();
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $user = $this->getUser();
                    $project->setOwner($user);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($project);
                    $em->flush();

                    $aclProvider = $this->get('security.acl.provider');
                    $objectIdentity = ObjectIdentity::fromDomainObject($project);
                    $acl = $aclProvider->createAcl($objectIdentity);

                    $securityContext = $this->get('security.context');
                    $user = $securityContext->getToken()->getUser();
                    $securityIdentity = UserSecurityIdentity::fromAccount($user);

                    $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                    $aclProvider->updateAcl($acl);

                    $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
                    $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                    $aclProvider->updateAcl($acl);

                    return $this->redirect($this->generateUrl('tangara_project_published'));
                }
            }
            return $this->renderContent('TangaraCoreBundle:Project:create_project.html.twig', 'project', array('form' => $form->createView()));
        }
    }

    public function memberAction($projectId) {
        $request = $this->getRequest();
        $session = $request->getSession();
        $manager = $this->get('tangara_core.project_manager');
        $project = $manager->getRepository()->find($projectId);
        $form = $this->createForm(new SearchType());
        if (empty($project)) {
            return $this->redirect($this->generateUrl('tangara_project_show', array('projectId' => $projectId)));
        }
        $request->getSession()->set('project', $project);

        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($project);
        $acl = $aclProvider->findAcl($objectIdentity);
        
        foreach ($acl->getObjectIdentity() as $key => $value){
            echo '<pre>';
            var_dump($value);
            echo '<pre>';
            echo '<br>';
            
        }
        
        
        return $this->renderContent('TangaraCoreBundle:Project:member.html.twig', 'project', array('project' => $project, 'form' => $form->createView()));
    }

    public function search_memberAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->get('search');
            $user = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('TangaraCoreBundle:User')
                    ->searchData($data);
            $project = $request->getSession()->get('project');
            $html = $this->renderView('TangaraCoreBundle:Project:result_member.html.twig', array('user' => $user, 'project' => $project));
            $response = new Response($html);
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        } else {
            $form = $this->createForm(new SearchType());
            return $this->renderContent('TangaraCoreBundle:Admin:users_ajax.html.twig', 'profile', array('form' => $form->createView()));
        }
    }

    public function add_memberAction($user, $project) {
        $object = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('TangaraCoreBundle:Project')
                ->find($project);
        $member = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('TangaraCoreBundle:User')
                ->find($user);
        if (empty($object) || empty($member) && !isset($member) && !isset($object)) {
            return $this->redirect($this->generateUrl('tangara_project_member', array('projectId' => $project)));
        }
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $acl = $aclProvider->findAcl($objectIdentity);
        $securityIdentity = UserSecurityIdentity::fromAccount($member);
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);
        return $this->redirect($this->generateUrl('tangara_project_show', array('projectId' => $project)));
    }

    public function add_member_ajaxAction() {
        $req = $this->getRequest();
        if ($req->isXmlHttpRequest()) {
            $userId = $req->get('userId');
            $projectId = $req->get('projectId');

            $object = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('TangaraCoreBundle:Project')
                    ->find($projectId);
            $member = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('TangaraCoreBundle:User')
                    ->find($userId);
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($object);
            $acl = $aclProvider->findAcl($objectIdentity);
            $securityIdentity = UserSecurityIdentity::fromAccount($member);
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }
    }
}
