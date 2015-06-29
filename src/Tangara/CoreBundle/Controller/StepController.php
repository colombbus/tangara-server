<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Entity\Step;

class StepController extends TangaraController {
   
    public function listAction($courseId) {
        $courseManager = $this->get('tangara_core.course_manager');
        $course = $courseManager->getRepository()->find($courseId);
        if (!$course || !$courseManager->mayEdit($course)) {
            // no course or no right to be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $repository = $this->get('tangara_core.step_manager')->getRepository();
        $params['admin'] = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $params['course'] = $course;
        $params['steps'] = $repository->getStepsNotIncluded($course);
        return $this->renderContent('TangaraCoreBundle:Step:list.html.twig', 'learn', $params);
    }

    public function pickAction($courseId, $stepId, Request $request) {
        $session = $request->getSession();
        // Check if course exists
        $courseManager = $this->get('tangara_core.course_manager');
        $course = $courseManager->getRepository()->find($courseId);
        // get manager
        $manager = $this->get('tangara_core.step_manager');
        // Check if project exists
        $step = $manager->getRepository()->find($stepId);
        if (!$course || !$step || !$manager->mayView($step)) {
            // no course or no step: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $manager->addToCourse($step, $course);
        return $this->redirect($this->generateUrl('tangara_course_steps', array('courseId'=>$courseId))); 
    }
    
    public function showAction($courseId, $stepId) {
        $params = array();
        // Check if course exists
        $courseManager = $this->get('tangara_core.course_manager');
        $course = $courseManager->getRepository()->find($courseId);
        // get manager
        $manager = $this->get('tangara_core.step_manager');
        // Check if project exists
        $step = $manager->getRepository()->find($stepId);
        if (!$course || !$step || !$manager->mayView($step)) {
            // no course or no step: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $params['course']=$course;
        $params['step']=$step;
        
        // Check user
        $params['edition'] = $manager->mayEdit($step);
        $params['admin'] = $this->get('security.context')->isGranted('ROLE_ADMIN');
        
        return $this->renderContent('TangaraCoreBundle:Step:show.html.twig', 'profile', $params);
    }
    
    public function createAction($courseId, Request $request ){
        $manager = $this->get('tangara_core.step_manager');
        $courseManager = $this->get('tangara_core.course_manager');
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $course = $courseManager->loadCourse($courseId);
        if (!$course) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $step = new Step();
        $form = $this->createForm('step', $step);
        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $manager->saveStep($step);
                $manager->addToCourse($step, $course);
                return $this->redirect($this->generateUrl('tangara_course_steps', array('courseId'=>$courseId)));
            }
        }
        return $this->renderContent('TangaraCoreBundle:Step:edit.html.twig', 'learn', array('form' => $form->createView(), 'title'=>'step.creation'));
    }
    
    
    public function editAction($courseId, $stepId, Request $request) {
        $session = $request->getSession();
        // Check if course exists
        $courseManager = $this->get('tangara_core.course_manager');
        $course = $courseManager->getRepository()->find($courseId);
        // get manager
        $manager = $this->get('tangara_core.step_manager');
        // Check if project exists
        $step = $manager->getRepository()->find($stepId);
        if (!$course || !$step || !$manager->mayView($step)) {
            // no course or no step: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $form = $this->createForm('step', $step);
        
        if ($request->isMethod('POST')) {
            // form submitted
            $form->handleRequest($request);
        
            if ($form->isValid()) {
                $manager->saveStep($step);
                $session->getFlashBag()->add('success', $this->get('translator')->trans('step.edited'));
                return $this->redirect($this->generateUrl('tangara_step_show', array('courseId' => $courseId, 'stepId' => $stepId)));                
            }
        }
        if (isset($form->attr)) {
            $form->attr = array_merge($form->attr, array('class'=>'form-content'));
        } else {
            $form->attr = array('class'=>'form-content');
        }
        return $this->renderContent('TangaraCoreBundle:Step:edit.html.twig', 'learn', array('form' => $form->createView(), 'title'=>'step.edition'));
    }
    
    public function selectAction($courseId, $stepId) {
        // get manager
        $manager = $this->get('tangara_core.project_manager');
        $project = $manager->getRepository()->find($projectId);
        if (!$manager->maySelect($project)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $this->getRequest()->getSession()->set('projectid', $projectId);
        return $this->renderContent('TangaraCoreBundle:Project:select.html.twig', 'create');
    }

    public function removeAction($courseId, $stepId, Request $request) {
        $session = $request->getSession();
        $courseManager = $this->get('tangara_core.course_manager');
        $course = $courseManager->getRepository()->find($courseId);
        $manager = $this->get('tangara_core.step_manager');
        $step = $manager->getRepository()->find($stepId);
        if (!$course || !$step || !$manager->mayView($step)) {
            // no course or no step: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $manager->removeFromCourse($step, $course);
        $session->getFlashBag()->add('success', $this->get('translator')->trans('step.removed'));
        return $this->redirect($this->generateUrl('tangara_course_steps', array('courseId' => $courseId)));                
    }
    
    
}
