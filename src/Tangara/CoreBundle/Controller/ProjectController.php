<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Document;
use Tangara\CoreBundle\Entity\DocumentRepository;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Form\ProjectType;

class ProjectController extends Controller {

    public function indexAction() {
    }

    /**
     * Get user and group projects list
     * @return type
     */
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

    /**
     * Change project name
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return type
     */
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
        $request = $this->getRequest();
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $projectId = $project->getId();

        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $project->getId();
        $cat = 1;

        $document = new Document();
        $document->setUploadDir($projectPath);
        $form = $this->createFormBuilder($document)
                //->add('name')
                ->add('file')
                ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->bind($request);
            $em = $this->getDoctrine()->getManager();
            $document->setOwnerProject($project);
            $document->upload();

            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        return $this->render('TangaraCoreBundle:Project:upload.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    
    
     /**
      * Create a new project
      * @param type $user_id
      * @param type $group_id
      * @return \Symfony\Component\HttpFoundation\Response
      */
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
    
    
    
    
    /**
     * Show all information about user and group projects
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return type
     */
    public function showAction(Project $project) {

        $contributors = array("user1", "user2", "user6");
        $manager = $this->getDoctrine()->getManager();
        $files = $manager->getRepository('TangaraCoreBundle:Document')->findBy(array('ownerProject' => $project->getId()));

        return $this->render('TangaraCoreBundle:Project:show.html.twig', array(
                    'project' => $project,
                    'contributors' => $contributors,
                    'files' => $files
        ));
    }
    
    /**
     * Remove user or group project 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function delProjectAction(){
        
        $projectid = $this->get('request')->get('projectid');
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('TangaraCoreBundle:Project');
        $project = $repository->find($projectid);
     

        $docs = $em->getRepository('TangaraCoreBundle:Document')
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
}
