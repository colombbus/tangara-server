<?php

namespace Tangara\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Tangara\ProjectBundle\Form\ProjectType;
use Tangara\ProjectBundle\Entity\Document;
use Tangara\ProjectBundle\Entity\Project;


class DefaultController extends Controller {
    public function indexAction() {
        $id = $this->get('session');
        return $this->render('TangaraProjectBundle:Default:index.html.twig');
    }

    public function showAction($id) {
        return $this->render('TangaraProjectBundle:Default:show.html.twig');
    }
    
    public function addAction() {
        $id = $this->container->get('security.context')->getToken()->getUser()->getId();
        $rootDir = $this->get('kernel')->locateResource('@app/config.yml', null, true);
        
        echo $rootDir;
        $fs = new Filesystem();
        //$fs->copy($originFile, $targetFile)
        
        $fs->mkdir('C:\tangara\\' . $id);
        //if ($fs->exists('/home/tangara/'..)){
            
        //}
         if ($this->getRequest()->isMethod('POST')) {
             echo 'request'. $this->getRequest();
         }
        
        $manager = $this->getDoctrine()->getManager();
        $project = new Project();
        //$project->setDateCreation(new \DateTime());
        $form = $this->createForm(new ProjectType(), $project);

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
