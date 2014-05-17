<?php

namespace Tangara\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tangara\ProjectBundle\Form\ProjectType;
use Tangara\ProjectBundle\Entity\Project;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TangaraProjectBundle:Default:index.html.twig');
    }
    public function addProjectAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $project = new Project();
        //$project->setDateCreation(new \DateTime());
        $form = $this->createForm(new ProjectType() , $project);
        
        return $this->render('TangaraProjectBundle:Default:addProject.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
