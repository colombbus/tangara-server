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

class DefaultController extends Controller {

    public function indexAction() {
        $id = $this->get('session');
        return $this->render('TangaraProjectBundle:Default:index.html.twig');
    }

    public function showAction(Project $project) {
        $user = $this->get('security.context')->getToken()->getUser();

        return $this->render('TangaraProjectBundle:Default:show.html.twig', array(
                    'project' => $project,
                    'user' => $user
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
        $manager->getRepository('TangaraProjectBundle:Project')->find('3');
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

    public function addAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $userId = $user->getId();

        //echo $rootDir;
        $fs = new Filesystem();
        //if ($fs->exists('/home/tangara/'.$id)){
        //}
        //else
        //$fs->mkdir('C:\tangara\\' . $id);
        //$fs->copy($originFile, $targetFile)

        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $project = new Project();
        $project->setProjectManager($user);

        //$project->setDateCreation(new \DateTime());
        $form = $this->createForm(new ProjectType(), $project);

        if ($request->isMethod('POST')) {
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();

            $em->persist($project);
            $em->flush();
            echo 'nouv :' . $project->getId();
            return $this->redirect($this->generateUrl('tangara_project_show', array('id' => $project->getId())
            ));
        }

        return $this->render('TangaraProjectBundle:Default:add.html.twig', array(
                'form' => $form->createView(),
                'userid' => $userId,
                'username' => $user
        ));
    }

    public function uploadAction() {
        $document = new Document();
        $form = $this->createFormBuilder($document)
                ->add('name')
                ->add('file')
                ->getForm()
        ;
        $fs = new Filesystem();
        //$fs->copy($originFile, $targetFile)
        $fs->mkdir('C:\tangara\\' . $id);
        //
        //$fs->mkdir('C:\tangara\\' . $id); // projects
        $fs->mkdir('C:\tangara\user'.$id); // perso projects
        //
        //if ($fs->exists('/home/tangara/'..)){
        //}
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                
                $em->persist($document);
                $em->flush();
                
                //$this->redirect($this->generateUrl(...));
            }
        }
        return $this->render('TangaraProjectBundle:Default:upload.html.twig', array(
                    'form' => $form->createView()
        ));
    }

}
