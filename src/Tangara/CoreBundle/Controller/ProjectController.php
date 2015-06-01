<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Entity\Project;

class ProjectController extends TangaraController {
   
    public function showAction($projectId) {
        $params = array();
        // Check if project id set
        $request = $this->getRequest();
        $session = $request->getSession();
        if ($projectId === false) {
            $projectId = $session->get('projectid');
        }
        if (!$projectId) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }
        
        // get manager
        $manager = $this->get('tangara_core.project_manager');

        // Check if project exists
        $project = $manager->getRepository()->find($projectId);
        if (!$project) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }
        
        $params['project']=$project;
        
        
        // Check access
        if (!$manager->mayView($project)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        // Check user
        $params['edition'] = $manager->mayEdit($project);
        $params['owner'] = $manager->isOwner($project);
        $params['admin'] = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $params['selectable'] = $manager->maySelect($project);
        
        if ($this->isUserLogged()){
            if ($manager->isHomeProject($project, $this->getUser())) {
                $params['message'] = "project.home_project";
            }
        }
        
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
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }

        // Check if project exists
        $project = $manager->getRepository()->find($projectId);
        if (!$project) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }
                
        // Check access
        if (!$manager->mayEdit($project)) {            
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
            $form->attr = array_merge($form->attr, array('class'=>'form-content'));
        } else {
            $form->attr = array('class'=>'form-content');
        }
        return $this->renderContent('TangaraCoreBundle:Project:edit.html.twig', 'project', array('form' => $form->createView(), 'title'=>'project.edition'));
    }
    
    
    public function publishedAction(Request $request) {
        $session = $request->getSession();
        $page = $session->get("published_projects_page", 1);
        $search = $session->get("published_projects_search", false);
        return $this->renderContent('TangaraCoreBundle:Project:published.html.twig', 'discover', array('page' => $page, 'search' => $search, 'paginationRoute' => 'tangara_project_published_list'));
    }

    public function getPublishedAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $this->redirect($this->generateUrl('tangara_project_published'));
        }
        $pagination = $this->get('tangara_core.pagination')->paginate($request, "admin_users", 'TangaraCoreBundle:Project', true);
        return $this->render('TangaraCoreBundle:Project:published_list.html.twig', array('projects' => $pagination));

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
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }
        
        // check access
        if (!$manager->mayExecute($project)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));            
        }
        
        // check that launcher is set and exists
        $launcher = $project->getLauncher();
        if (!isset($launcher)) {
            //TODO: check that file actually exists
            $params['error'] = 'project.launcher_not_set';
        }
        
        $params['project'] = $project;

        $width = $project->getWidth();
        if (isset($width) && $width>0) {
            $params['width'] = $width;
        }

        $height = $project->getHeight();
        if (isset($height) && $height>0) {
            $params['height'] = $height;
        }
        
        $tangarajs = $this->generateUrl('tangara_core_homepage').$this->container->getParameter('tangara_core.settings.directory.tangarajs')."/execute.html";
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
        if (!$manager->maySelect($project)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $this->getRequest()->getSession()->set('projectid', $projectId);
        return $this->renderContent('TangaraCoreBundle:Project:select.html.twig', 'create');
    }
    
    //action for create a news prject
    public function createAction(Request $request ){        
        $manager = $this->get('tangara_core.project_manager');
        if (!$this->isUserLogged()) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        else {
            $project = new Project();
            $form = $this->createForm('project', $project);
            if ($request->isMethod('POST')){
                $form->handleRequest($request);
                if ($form->isValid()){
                    $manager->saveProject($project);
                    $user = $this->getUser();
                    $manager->setOwner($project, $user, true);
                    return $this->redirect($this->generateUrl('tangara_project_show', array('projectId'=>$project->getId())));
                }
            }
            return $this->renderContent('TangaraCoreBundle:Project:edit.html.twig', 'project', array('form' => $form->createView(), 'title'=>'project.creation'));
            
        }
    }
}
