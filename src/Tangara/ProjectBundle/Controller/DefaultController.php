<?php

namespace Tangara\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\Response;
use Tangara\ProjectBundle\Form\ProjectType;
use Tangara\ProjectBundle\Entity\Document;
use Tangara\ProjectBundle\Entity\Project;
use Tangara\UserBundle\Entity\User;
use Tangara\UserBundle\Entity\Group;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('TangaraProjectBundle:Default:index.html.twig');
    }

    public function showAction(Project $project) {
        $user = $this->get('security.context')->getToken()->getUser();

        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $project = $manager->getRepository('TangaraProjectBundle:Project')->find($project->getId());

        $lists = $project->getContributors();

        return $this->render('TangaraProjectBundle:Default:show.html.twig', array(
                    'project' => $project,
                    'user' => $user,
                    'lists' => $lists
        ));
    }

    public function listAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $projects = $manager->getRepository('TangaraProjectBundle:Project')->findAll();


        echo $this->get('tangara_project.uploader')->getUploadDirectory();

        $repository = $manager->getRepository('TangaraProjectBundle:Project');
        $admin = '"admin"';

        //$conn = $this->get('database_connection');
        //$different = $conn->fetchAll('SELECT ProjectManager FROM project WHERE ProjectManager != '.$admin);


        $query = $repository->createQueryBuilder('project')
                ->where('project.projectManager != :ProjectManager')
                ->setParameter('ProjectManager', 'admin')
                ->getQuery();

        $different = $query->getResult();



        return $this->render('TangaraProjectBundle:Default:list.html.twig', array(
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

        return $this->render('TangaraProjectBundle:Default:edit.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'project' => $project
        ));
    }

    public function createAction() {
        //echo '**' . $this->get('kernel')->getRootDir() . '**';
        return $this->render('TangaraProjectBundle:Default:create.html.twig');
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

        return $this->render('TangaraProjectBundle:Default:new.html.twig', array(
                    'form' => $form->createView(),
                    'userid' => $user_id,
                    'username' => $user,
                    'project' => $project,
                    'project_owner_group' => $group_member
        ));
    }

    public function uploadAction() {
        //$id = $request->get('security.context')->getToken()->getUser()->getId();
        // DEVELOP ONLY
        $project_id = 23;
        $user_id = 2;
        $base_path = 'C:\tangara';
        $project_user_path = $base_path . "/" . $user_id;
        $project_path = $base_path . "/" . $project_id;

        $request = $this->getRequest();

        $document = new Document();
        $form = $this->createFormBuilder($document)
                ->add('name')
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

        return $this->render('TangaraProjectBundle:Default:upload.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    public function getAjaxAction() {
        $data = "ok";
        $request = $this->getRequest();

        $data = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraProjectBundle:Project')
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

}
