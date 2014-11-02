<?php

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\File;
use Tangara\CoreBundle\Entity\FileRepository;
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
        // get manager
        $manager = $this->get('tangara_core.project_manager');

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
        
        $params['project']=$project;
        
        // Check user
        $edition = false;
        $owner = false;
        if ($this->isUserLogged()) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $auth = $manager->isAuthorized($project, $user);
            if ($auth) {
                $edition = true;
                if ($manager->isHomeProject($project, $user)) {
                    $params['message'] = "project.home_project";
                }
                if ($project->getOwner() === $user) {
                    $owner = true;
                }
            }
        }
        
        if (!$edition) {
            // Check that project is public
            if (!$project->getPublished()) {
                // User not authorized
                return $this->redirect($this->generateUrl('tangara_core_homepage'));
            }
        }
        
        $params['edition'] = $edition;
        $params['owner'] = $owner;
        return $this->renderContent('TangaraCoreBundle:Project:show.html.twig', 'project', $params);
    }
    
    public function editAction($projectId) {
        // Check if project id set
        $request = $this->getRequest();
        $session = $request->getSession();
        // get manager
        $manager = $this->get('tangara_core.project_manager');
        
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
        
        // Check user
        $user = $this->container->get('security.context')->getToken()->getUser();

        // Check project access by user
        $auth = $manager->isAuthorized($project, $user);
        if (!$auth) {
            // User not authorized
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $form = $this->createForm('project', $project);
        
        if ($request->isMethod('POST')) {
            // form submitted
            $form->handleRequest($request);
        
            if ($form->isValid()) {
                $manager->saveProject($project);
                $session->getFlashBag()->add('success', $this->get('translator')->trans('project.edited'));                
                return $this->redirect($this->generateUrl('tangara_project_show', array('cat' => 1, 'id' => $project->getId())));                
            }
        }
        if (isset($form->attr)) {
            $form->attr = array_merge($form->attr, array('class'=>'form-content'));
        } else {
            $form->attr = array('class'=>'form-content');
        }
        return $this->renderContent('TangaraCoreBundle:Project:edit.html.twig', 'project', array('form' => $form->createView()));
    }
    
    
    public function publishedAction() {
        $projects = $this->get('tangara_core.project_manager')->getRepository()->getPublishedProjects();
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
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
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
        
        $access = false;
        if ($this->isUserLogged()) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $auth = $manager->isAuthorized($project, $user);
            if ($auth) {
                $access = true;
            }
        }
        
        if (!$access) {
            // Check that project is public
            if (!$project->getPublished()) {
                // User not authorized
                return $this->redirect($this->generateUrl('tangara_core_homepage'));
            }
        }
        $tangarajs = $this->generateUrl('tangara_core_homepage').$this->container->getParameter('tangara_core.settings.directory.tangarajs');
        $params['tangarajs'] = $tangarajs;
        return $this->render('TangaraCoreBundle:Project:execute.html.twig', $params);
        
    }
    
    /*public function indexAction() {
    }
    
    
    
    

    public function listAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $projectManager = $user;
        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $projects = $manager->getRepository('TangaraCoreBundle:Project')->findAll();

        $repository = $manager->getRepository('TangaraCoreBundle:Project');

        $query = $repository->createQueryBuilder('project')
                ->where('project.projectManager != :ProjectManager')
                ->setParameter('ProjectManager', $projectManager)
                ->getQuery();

        $different = $query->getResult();

        return $this->render('TangaraCoreBundle:Project:list.html.twig', array(
                    'projects' => $projects,
                    'different' => $different
        ));
    }

    public function editAction(Project $project) {
        $user = $this->get('security.context')->getToken()->getUser();

        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ProjectType(), $project);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid())
                $p = $form->getData();

            $manager->persist($project);
            $manager->flush();

            return $this->redirect($this->generateUrl('tangara_project_show', array('cat' => 1, 'id' => $project->getId())));
        }

        return $this->render('TangaraCoreBundle:Project:edit.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'project' => $project
        ));
    }

    
    public function createAction() {
        $tangarajs = $this->container->getParameter('tangara_core.settings.directory.tangarajs');
        //if ($tangarajs == null) {}
        $fileToOpen = $this->get('request')->get('projectid');

        return $this->render('TangaraCoreBundle:Project:create.html.twig', array(
                    'tangarajs' => $tangarajs,
                    'projectid' => $fileToOpen
        ));
    }

    
    public function uploadAction(Project $project) {
//        $request = $this->getRequest();
//        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
//        $projectId = $project->getId();
//
//        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
//        $projectPath = $uploadPath . '/' . $project->getId();
//        $cat = 1;
//
//        $document = new Document();
//        $document->setUploadDir($projectPath);
//        $form = $this->createFormBuilder($document)
//                //->add('name')
//                ->add('file')
//                ->getForm()
//        ;
//
//        if ($request->isMethod('POST')) {
//            $form->bind($request);
//            $em = $this->getDoctrine()->getManager();
//            $document->setOwnerProject($project);
//            $document->upload();
//
//            $em->persist($document);
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('tangara_core_homepage'));
//        }
//
//        return $this->render('TangaraCoreBundle:Project:upload.html.twig', array(
//                    'form' => $form->createView()
//        ));
        
        
        
        
        
    }

    
    public function newAction($user_id, $group_id) {

        $user = $this->get('security.context')->getToken()->getUser();

        $userId = $user->getId();

        $project = new Project();
        $project->setProjectManager($user);

        $projectId = $project->getId();


        $base_path = 'C:/tangara/';
        $project_user_path = $base_path . $userId;
        $project_path = $base_path . $projectId;

        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $group_member = $user->getGroups();

        $form = $this->createForm(new ProjectType(), $project);
        
        if ($request->isMethod('POST')) {

            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();
            $p = new Project();
        
            if($user_id){           
                $allProjects = $user->getProjects();
                $projectExist = $p->isUserProjectExist($allProjects, $project->getName());

                $user->addProjects($project);
                $project->setUser($user);
            }
            else if($group_id){
                $group = $em->getRepository('TangaraCoreBundle:Group')->find($group_id);

                $allProjects = $group->getProjects();
                $projectExist = $p->isGroupProjectExist($allProjects, $project->getName());

                $group->addProjects($project);
                $project->setGroup($group);
            }

            if ($projectExist == false) {
                $em->persist($project);
                $em->flush();
                return $this->redirect($this->generateUrl('tangara_project_show', array('id' => $project->getId())
                ));
            }
            return new Response('A project with the same name already exists.');
            
        }

        return $this->render('TangaraCoreBundle:Project:new.html.twig', array(
                    'form' => $form->createView(),
                    'userid' => $userId,
                    'username' => $user,
                    'project' => $project,
                    'project_owner_group' => $group_member,
                    'g_id' => $group_id,
                    'u_id' => $user_id
        ));
    }
    
    
    
    
    public function showAction(Project $project) {

        $contributors = array("user1", "user2", "user6");
        $manager = $this->getDoctrine()->getManager();
        $files = $manager->getRepository('TangaraCoreBundle:File')->findBy(array('ownerProject' => $project->getId()));

        return $this->render('TangaraCoreBundle:Project:show.html.twig', array(
                    'project' => $project,
                    'contributors' => $contributors,
                    'files' => $files
        ));
    }
    
    function delProjectAction(){
        
        $projectid = $this->get('request')->get('projectid');
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('TangaraCoreBundle:Project');
        $project = $repository->find($projectid);
     

        $docs = $em->getRepository('TangaraCoreBundle:File')
                ->getAllProjectDocuments($project->getName());

        foreach ($docs as $key){           
            $em->remove($key);
        }
        
        $em->remove($project);
        $em->flush(); 
   
        if($docs){
            echo "Files have been deleted.";
        }
        else{
            echo "There are no documents in this project.";
        }
        
        return new Response(NULL); 
        
    }
    */
}
