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

class DefaultController extends Controller {

    public function indexAction() {
        $id = $this->get('session');
        return $this->render('TangaraProjectBundle:Default:index.html.twig');
    }

    public function showAction(Project $project) {
        $user = new User(); 
        if ($this->get('security.context')->isGranted('ROLE_USER'))
            $user = $this->get('security.context')->getToken()->getUser();
        return $this->render('TangaraProjectBundle:Default:show.html.twig', array(
                    'project' => $project,
                    'user' => $user
        ));
    }

    public function editAction(Project $project) {
        $user = $this->get('security.context')->getToken()->getUser();

        //$rootDir = $this->get('kernel')->locateResource('@app/config.yml', null, true);
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

        //$project->setDateCreation(new \DateTime());
        $form = $this->createForm(new ProjectType(), $project);

        if ($request->isMethod('POST')) {
            $form->bind($this->getRequest());
            echo 'request' . $this->getRequest();
            $em = $this->getDoctrine()->getManager();

            $em->persist($project);
            $em->flush();
        }

        return $this->render('TangaraProjectBundle:Default:edit.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user
        ));
    }

    public function addAction() {
        $id = $this->get('security.context')->getToken()->getUser()->getId();

        //$rootDir = $this->get('kernel')->locateResource('@app/config.yml', null, true);
        //echo $rootDir;
        $fs = new Filesystem();
        //$fs->copy($originFile, $targetFile)
        //$fs->mkdir('C:\tangara\\' . $id);
        //
        //if ($fs->exists('/home/tangara/'..)){
        //}


        $manager = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $project = new Project();

        //$project->setDateCreation(new \DateTime());
        $form = $this->createForm(new ProjectType(), $project);

        if ($request->isMethod('POST')) {
            $form->bind($this->getRequest());
            echo 'request' . $this->getRequest();
            $em = $this->getDoctrine()->getManager();

            $em->persist($project);
            $em->flush();
        }

        return $this->render('TangaraProjectBundle:Default:add.html.twig', array(
                    'form' => $form->createView(),
                    'id' => $id
        ));
    }

    public function uploadAction() {
        $document = new Document();
        $form = $this->createFormBuilder($document)
                ->add('name')
                ->add('file')
                ->getForm()
        ;
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
