<?php

namespace Tangara\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Tangara\ProjectBundle\Form\ProjectType;
use Tangara\ProjectBundle\Entity\Document;
use Tangara\ProjectBundle\Entity\Project;
use Tangara\UserBundle\Entity\User;
use Tangara\UserBundle\Entity\Group;

class FileController extends Controller {

    public function indexAction() {
        $id = $this->get('session');
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

        return $this->render('TangaraProjectBundle:Default:list.html.twig', array(
                    'projects' => $projects
        ));
    }

    public function editAction(Project $project) {
        $user = $this->get('security.context')->getToken()->getUser();

        //echo $rootDir;
        $fs = new Filesystem();
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

        $project = new Project();
        // $adminsGroup = new Group('regissadores');
        // $user->addGroups($adminsGroup);
        $manager->persist($project);
        $manager->flush();

        //$project->setDateCreation(new \DateTime());
        $form = $this->createForm(new ProjectType(), $project);
        //$form->bind($project);
        //$request->query->get('page'); // retourne un paramètre $_GET
        //$request->request->get('page'); // retourne un paramètre $_POST

        if ($request->isMethod('GET')) {
//            $form->bind($this->getRequest());
//            echo 'request' . $this->getRequest();
//            $em = $this->getDoctrine()->getManager();
//
//            $em->persist($project);
//            $em->flush();
//            $argu = $request->query->get('page');
//            echo "requete " . $argu;
        }

        return $this->render('TangaraProjectBundle:Default:edit.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'project' => $project
        ));
    }
    
    public function createAction() {
        return $this->render('TangaraProjectBundle:Default:create.html.twig');
    }
    
    public function newAction() {
        $user = $this->get('security.context')->getToken()->getUser();
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

            if (!$fs->exists($project_user_path)) {
                $fs->mkdir($project_user_path); // perso projects
            }

            if (!$fs->exists($project_path)) {
                $fs->mkdir($project_path); // perso projects
            }
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
}
