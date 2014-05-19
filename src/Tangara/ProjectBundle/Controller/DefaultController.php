<?php

namespace Tangara\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tangara\ProjectBundle\Form\ProjectType;
use Tangara\ProjectBundle\Entity\Document;
use Tangara\ProjectBundle\Entity\Project;

class DefaultController extends Controller {

    public function indexAction($projectName) {
        
        
        return $this->render('TangaraProjectBundle:Default:index.html.twig');
    }

    public function addProjectAction() {
        $manager = $this->getDoctrine()->getManager();
        $project = new Project();
        //$project->setDateCreation(new \DateTime());
        $form = $this->createForm(new ProjectType(), $project);

        return $this->render('TangaraProjectBundle:Default:addProject.html.twig', array(
                    'form' => $form->createView()
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
