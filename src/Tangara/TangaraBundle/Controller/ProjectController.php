<?php

namespace Tangara\TangaraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Tangara\TangaraBundle\Entity\Document;
use Tangara\TangaraBundle\Entity\Project;
use Tangara\TangaraBundle\Form\ProjectType;

class ProjectController extends Controller {

    public function indexAction() {
        return $this->redirect($this->generateUrl('tangara_tangara_homepage'));
    }

    public function showAction(Project $project) {
        $user = $this->get('security.context')->getToken()->getUser();

        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $pid = $project->getId();
        $project = $manager->getRepository('TangaraTangaraBundle:Project')->find($pid);

        //var_dump($project->getGroup());
        // list of user contributors of a project
        $contributors = array("user1", "user2", "user6");
        $files = array("heros.png", "bad.jpg", "Cours01.tgr", "Cours2.tgr", "Cours11.tgr", );
        $project->getGroup();

        return $this->render('TangaraTangaraBundle:Project:show.html.twig', array(
                    'project' => $project,
                    'user' => $user,
                    'contributors' => $contributors,
                    'files' => $files
        ));
    }

    public function listAction() {

        $user = $this->get('security.context')->getToken()->getUser();
        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $projects = $manager->getRepository('TangaraTangaraBundle:Project')->findAll();


        //echo $this->get('tangara_project.uploader')->getUploadDirectory();

        $repository = $manager->getRepository('TangaraTangaraBundle:Project');
        $admin = '"admin"';

        //$conn = $this->get('database_connection');
        //$different = $conn->fetchAll('SELECT ProjectManager FROM project WHERE ProjectManager != '.$admin);

        $query = $repository->createQueryBuilder('project')
                ->where('project.projectManager != :ProjectManager')
                ->setParameter('ProjectManager', 'admin')
                ->getQuery();

        $different = $query->getResult();

        return $this->render('TangaraTangaraBundle:Project:list.html.twig', array(
                    'projects' => $projects,
                    'different' => $different
        ));
    }

    public function editAction(Project $project) {
        $user = $this->get('security.context')->getToken()->getUser();

        //$fs->copy($originFile, $targetFile)
        //$fs->mkdir('C:\tangara\\' . $id);
        //
        //$fs->mkdir('C:\tangara\\' . $id); // projects
        //$fs->mkdir('C:\tangara\user'.$id); // perso projects
        //
        //if ($fs->exists('/home/tangara/'..)){
        //}

        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $form = $this->createForm(new ProjectType(), $project);

        //$request->query->get('page'); // retourne un paramètre $_GET
        //$request->request->get('page'); // retourne un paramètre $_POST

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid())
                $p = $form->getData();

            $manager->persist($project);
            $manager->flush();

            return $this->redirect($this->generateUrl('tangara_project_show', array('id' => $project->getId())));
        }

        return $this->render('TangaraTangaraBundle:Project:edit.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'project' => $project
        ));
    }

    public function createAction() {
        //echo '**' . $this->get('kernel')->getRootDir() . '**';
        return $this->render('TangaraTangaraBundle:Project:create.html.twig');
    }

    public function newAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        // DEVELOP ONLY
        $user_id = $user->getId();

        $project = new Project();
        $project->setProjectManager($user);

        $project_id = $project->getId();

        $base_path = 'C:/tangara/';
        $project_user_path = $base_path . $user_id;
        $project_path = $base_path . $project_id;

        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $group_member = $user->getGroups();

        $form = $this->createForm(new ProjectType(), $project);

        if ($request->isMethod('POST')) {
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();
            //$this->file->move($project_path, $this->file->getClientOriginalName());

            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('tangara_project_show', array('id' => $project->getId())
            ));
        }

        return $this->render('TangaraTangaraBundle:Project:new.html.twig', array(
                    'form' => $form->createView(),
                    'userid' => $user_id,
                    'username' => $user,
                    'project' => $project,
                    'project_owner_group' => $group_member
        ));
    }

    public function uploadAction(Project $project) {
        $request = $this->getRequest();
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $project_id = $project->getId();

        //$base_path = $this->get('tangara_project.uploader');
        $base_path = '/home/elise/NetBeansProjects/tangara-data';
        $project_user_path = $base_path . "/" . $user_id;
        $project_path = $base_path . "/" . $project_id;


        $document = new Document();
        $form = $this->createFormBuilder($document)
                //->add('name')
                ->add('file')
                ->getForm()
        ;
        $fs = new Filesystem();

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();


            $document->upload();
            //$file_uploaded = $request->get('file');
            //$fs->copy($file_uploaded, $project_user_path);
            $em->persist($document);
            $em->flush();

            //$ret = 'done ' . $file_uploaded ; 
            //return new \Symfony\Component\HttpFoundation\Response($ret);
        }

        return $this->render('TangaraTangaraBundle:Project:upload.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    public function getAjaxAction() {
        $data = "ok";
        $request = $this->getRequest();

        $data = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraTangaraBundle:Project')
                ->myFindAll();

        if ($this->getRequest()->isMethod('POST')) {
            $data = $request->request->get('data');
            //var_dump($request->request->all());
        }
        if ($this->getRequest()) {
            //$this->getRequest()->request();

            return new Response('Reçu en POST : ' . $data);
        }

        //return new Response('<h1>Reçu en normal</h1>');
    }

    //controleur vers la page de confirmation
    public function confirmationAction() {
        return $this->render('TangaraTangaraBundle:Project:confirmation.html.twig');
    }
    
    public function testAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        
        echo $user->getId();
        
    }
    
    /*
    public function ifGroupMemberAction(){
        
        $user = $this->get('security.context')->getToken()->getUser();
        $repository = $this->getDoctrine()->getManager()->getRepository('TangaraProjectBundle:Project');
       
        $query = $repository->createQueryBuilder('p')
                ->where('p.id = 1')
                ->getQuery();

    /*
      public function ifGroupMemberAction(){

      $user = $this->get('security.context')->getToken()->getUser();
      $repository = $this->getDoctrine()->getManager()->getRepository('TangaraProjectBundle:Project');

      $query = $repository->createQueryBuilder('p')
      ->where('p.id = 1')
      ->getQuery();

      $project = $query->getResult();
      return new Response($project[0]->getName());
      }


      public function listGroupAction(){
      //connexion a la base de donnee
      $user = $this->get('security.context')->getToken()->getUser();
      $list = $user->getGroups();

      foreach($list as $key){
      echo $key->getName();
      }

      //return $this->render('TangaraProjectBundle:Default:page.html.twig', );
      }


      public function listProjetAction(){
      //connexion a la base de donnee
      $user = $this->get('security.context')->getToken()->getUser();
      $list = $user->getProjects();

      foreach($list as $key){
      echo $key->getName();
      }
      }
     */

    /*
      public function listNoGroupAction(){
      $user = $this->get('security.context')->getToken()->getUser();
      $em = $this->getDoctrine()->getManager();

      $repository_group = $em->getRepository('TangaraUserBundle:Group');

      //tous les groupes
      $allgroups = $repository_group->findAll();
      //les groupes aux quels l'user appartient
      $user_groups = $user->getGroups();

      $tmp = groupsWithoutMe($allgroups, $user_groups);

      return $this->render('TangaraProjectBundle:Default:list_no_group_content.html.twig', array('groups' => $tmp));
      }
     */
}

/**
 * Request group list that user isn't member
 * 
 * @param array $allgroups 
 * @param array $user_groups
 * @return groupsWithoutMe list that user isn't member
 */
function groupsWithoutMe($allgroups, $user_groups) {

    foreach ($allgroups as $all) {
        $trigger = true;
        foreach ($user_groups as $ug) {
            if ($all->getName() == $ug->getName()) {
                $trigger = false;
                break;
            }
        }
        if ($trigger == true) {
            $groupsWithoutMe[] = $all;
        }
    }

    return $groupsWithoutMe;
}
