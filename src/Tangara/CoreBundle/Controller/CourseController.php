<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Entity\Course;

class CourseController extends TangaraController {
   
    public function listAction() {
        $repository = $this->get('tangara_core.course_manager')->getRepository();
        $params['admin'] = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $params['courses'] = $repository->getCourses();
        return $this->renderContent('TangaraCoreBundle:Course:list.html.twig', 'learn', $params);
    }
    
    public function showAction($courseId) {
        $params = array();
        // Check if course id is set
        $request = $this->getRequest();
        $session = $request->getSession();
        if ($courseId === false) {
            $courseId = $session->get('courseid');
        }
        if (!$courseId) {
            // no current course: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        // get manager
        $manager = $this->get('tangara_core.course_manager');

        // Check if project exists
        $course = $manager->getRepository()->find($courseId);
        if (!$course) {
            // no course: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $params['course']=$course;
        
        
        // Check access
        if (!$manager->mayView($course)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        // Check user
        $params['edition'] = $manager->mayEdit($course);
        $params['admin'] = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $params['executable'] = $manager->mayExecute($course);
        
        return $this->renderContent('TangaraCoreBundle:Course:show.html.twig', 'profile', $params);
    }
    
    public function createAction(Request $request ){        
        $manager = $this->get('tangara_core.course_manager');
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        else {
            $course = new Course();
            $form = $this->createForm('course', $course);
            if ($request->isMethod('POST')){
                $form->handleRequest($request);
                if ($form->isValid()){
                    $manager->saveCourse($course);
                    return $this->redirect($this->generateUrl('tangara_course_show', array('courseId'=>$course->getId())));
                }
            }
            return $this->renderContent('TangaraCoreBundle:Course:edit.html.twig', 'learn', array('form' => $form->createView(), 'title'=>'course.creation'));
        }
    }
    
    
    public function editAction($courseId) {
        // Check if course id set
        $request = $this->getRequest();
        $session = $request->getSession();
        // get manager
        $manager = $this->get('tangara_core.course_manager');

        if ($courseId === false) {
            $courseId = $session->get('courseid');
        }   
        
        if (!$courseId) {
            // no current course: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        // Check if course exists
        $course = $manager->getRepository()->find($courseId);
        if (!$course) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
                
        // Check access
        if (!$manager->mayEdit($course)) {            
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $form = $this->createForm('course', $course);
        
        if ($request->isMethod('POST')) {
            // form submitted
            $form->handleRequest($request);
        
            if ($form->isValid()) {
                $manager->saveCourse($course);
                $session->getFlashBag()->add('success', $this->get('translator')->trans('course.edited'));
                return $this->redirect($this->generateUrl('tangara_course_show', array('courseId' => $course->getId())));                
            }
        }
        if (isset($form->attr)) {
            $form->attr = array_merge($form->attr, array('class'=>'form-content'));
        } else {
            $form->attr = array('class'=>'form-content');
        }
        return $this->renderContent('TangaraCoreBundle:Course:edit.html.twig', 'learn', array('form' => $form->createView(), 'title'=>'course.edition'));
    }
    
    public function selectAction($projectId) {
        // get manager
        $manager = $this->get('tangara_core.project_manager');
        $project = $manager->getRepository()->find($projectId);
        if (!$manager->maySelect($project)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $this->getRequest()->getSession()->set('projectid', $projectId);
        return $this->renderContent('TangaraCoreBundle:Project:select.html.twig', 'create');
    }

    public function stepsAction($courseId) {
        // Check if course id set
        $request = $this->getRequest();
        $session = $request->getSession();
        // get manager
        $manager = $this->get('tangara_core.course_manager');

        if ($courseId === false) {
            $courseId = $session->get('courseid');
        }   
        
        if (!$courseId) {
            // no current course: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }

        // Check if course exists
        $course = $manager->getRepository()->find($courseId);
        if (!$course) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
                
        // Check access
        if (!$manager->mayEdit($course)) {            
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $params['course'] = $course;
        $params['steps'] = $manager->getSteps($course);
        
        return $this->renderContent('TangaraCoreBundle:Course:steps.html.twig', 'learn', $params);
    }
    
    
}
