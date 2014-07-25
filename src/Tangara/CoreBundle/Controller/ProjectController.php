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
        //return $this->redirect($this->generateUrl('tangara_core_homepage'));
    }

    public function listAction() {
    $user = $this->get('security.context')->getToken()->getUser();
        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $projects = $manager->getRepository('TangaraCoreBundle:Project')->findAll();

        $repository = $manager->getRepository('TangaraCoreBundle:Project');
        $admin = '"admin"';

        //$conn = $this->get('database_connection');
        //$different = $conn->fetchAll('SELECT ProjectManager FROM project WHERE ProjectManager != '.$admin);

        $query = $repository->createQueryBuilder('project')
                ->where('project.projectManager != :ProjectManager')
                ->setParameter('ProjectManager', 'admin')
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

    /**
     * 
     * Create a program with TangaraJS
     * 
     */
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

    /*
     * Create a new project
     */

    public function newGroupProjectAction($group_id = null) {

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

            $p = new Project();
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();

            $group = $em->getRepository('TangaraCoreBundle:Group')->find($group_id);

            $allProjects = $group->getProjects();
            $projectExist = $p->isGroupProjectExist($allProjects, $project->getName());

            $group->addProjects($project);
            $project->setGroup($group);


            if ($projectExist == false) {
                $em->persist($project);
                $em->flush();
                return $this->redirect($this->generateUrl('tangara_project_group_show', array('id' => $project->getId())
                ));
            }
            return new Response('Un projet avec me meme nom existe deja.');
        }

        return $this->render('TangaraCoreBundle:Project:new.html.twig', array(
                    'form' => $form->createView(),
                    'userid' => $userId,
                    'username' => $user,
                    'project' => $project,
                    'project_owner_group' => $group_member,
                    'g_id' => $group_id,
                    'u_id' => NULL,
        ));
    }

    public function newUserProjectAction($user_id = null) {

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
            $allProjects = $user->getProjects();
            $projectExist = $p->isUserProjectExist($allProjects, $project->getName());

            $user->addProjects($project);
            $project->setUser($user);


            if ($projectExist == false) {
                $em->persist($project);
                $em->flush();
                return $this->redirect($this->generateUrl('tangara_project_user_show', array('id' => $project->getId())
                ));
            }
            return new Response('Un projet avec me meme nom existe deja.');
        }

        return $this->render('TangaraCoreBundle:Project:new.html.twig', array(
                    'form' => $form->createView(),
                    'userid' => $userId,
                    'username' => $user,
                    'project' => $project,
                    'project_owner_group' => $group_member,
                    'g_id' => NULL,
                    'u_id' => $user_id
        ));
    }

    //group project info
    public function showGroupProjectsAction(Project $project) {
        //view for group project
        return $this->render('TangaraCoreBundle:Project:show_group_project.html.twig', array('project' => $project));
    }

    //user project info
    public function showUserProjectsAction(Project $project) {
        
        
        //view for user project
        $contributors = array("user1", "user2", "user6");
        $manager = $this->getDoctrine()->getManager();
        $files = $manager->getRepository('TangaraCoreBundle:Document')->findBy(array('ownerProject' => $project->getId()));

        return $this->render('TangaraCoreBundle:Project:show_user_project.html.twig', array(
                    'project' => $project,
                    'contributors' => $contributors,
                    'files' => $files
        ));
    }

    //del user project
    function delUserAction() {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('TangaraCoreBundle:Project');
        $project = $repository->find($_GET['projectid']);
        $repositoryU = $em->getRepository('TangaraCoreBundle:User');
        $user = $repositoryU->find($_GET['userid']);

        //$this->get('request')->get('projectid');


        $docs = $em->getRepository('TangaraCoreBundle:Document')
                ->getAllProjectDocuments($project->getName());


        $tmp = " ";
        foreach ($docs as $key) {

            $em->remove($key);
            $tmp += $key->getId();
        }

        $em->remove($project);
        $em->flush();


        if ($docs) {
            echo "Les fichiers on ete supprimer." . $tmp;
        } else {
            echo "Il n'y a pas de document dans ce projet.";
        }


        //echo "Vous avez supprimmer le procjet id = ".$_GET['projectid'];

        return new Response(NULL);
    }

    //del user project
    function delGroupAction() {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('TangaraCoreBundle:Project');
        $project = $repository->find($_GET['projectid']);
        $repositoryU = $em->getRepository('TangaraCoreBundle:Group');
        $group = $repositoryU->find($_GET['groupid']);

        //$this->get('request')->get('projectid');


        $docs = $em->getRepository('TangaraCoreBundle:Document')
                ->getAllProjectDocuments($project->getName());


        //juste les id des docs
        $tmp = " ";
        foreach ($docs as $key) {

            $em->remove($key);
            $tmp += $key->getId();
        }

        $em->remove($project);
        $em->flush();


        if ($docs) {
            echo "Les documents on ete supprimer." . $tmp;
        } else {
            echo "Il n'y a pas de document dans ce projet.";
        }







        //echo "Vous avez supprimmer le procjet id = ".$_GET['projectid'];

        return new Response(NULL);
    }

}
