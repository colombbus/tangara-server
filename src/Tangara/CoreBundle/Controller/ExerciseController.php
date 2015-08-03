<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tangara\CoreBundle\Controller\TangaraController;
use Tangara\CoreBundle\Entity\Project;

class ExerciseController extends TangaraController {
   
    public function showAction($exerciseId,Request $request) {
        $session = $request->getSession();            
        $params = array();
        if ($exerciseId === false) {
            $session = $request->getSession();            
            $exerciseId = $session->get('projectid');
        }
        if (!$exerciseId) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        // get manager
        $manager = $this->get('tangara_core.project_manager');
        // Check if project exists
        $exercise = $manager->getRepository()->find($exerciseId);
        if (!$exercise) {
            // exercise does not exist: we should not be here
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }

        // Check access
        if (!$manager->mayView($exercise)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $params['exercise'] = $exercise;
        
        // Check user
        $params['edition'] = $manager->mayEdit($exercise);
        $params['owner'] = $manager->isOwner($exercise);
        $params['admin'] = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $params['selectable'] = $manager->maySelect($exercise);
        $params['exerciseUrl'] = $this->getRequest()->getSchemeAndHttpHost()."/".$this->container->getParameter('tangara_core.settings.directory.tangarajs')."/learn.html#".$exercise->getId();
        
        if ($session->get('userMenuUpdateRequired')) {
            $session->remove('userMenuUpdateRequired');
            $params['updateUserMenu'] = true;
        }
        
        return $this->renderContent('TangaraCoreBundle:Exercise:show.html.twig', 'profile', $params);
    }
    
    public function createAction(Request $request ){
        $manager = $this->get('tangara_core.project_manager');
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $exercise = new Project();
        $form = $this->createForm('project', $exercise, array('exercise'=>true));
        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $manager->saveExercise($exercise);
                $manager->createExerciseFiles($exercise);
                $user = $this->getUser();
                $manager->setOwner($exercise, $user, true);
                return $this->redirect($this->generateUrl('tangara_exercise_show', array('exerciseId'=>$exercise->getId())));
            }
        }
        return $this->renderContent('TangaraCoreBundle:Exercise:edit.html.twig', 'learn', array('form' => $form->createView(), 'title'=>'exercise.creation'));
    }
    
    
    public function editAction($exerciseId, Request $request) {
        $session = $request->getSession();            
        if ($exerciseId === false) {
            $exerciseId = $session->get('projectid');
        }
        
        if (!$exerciseId) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }
        // get manager
        $manager = $this->get('tangara_core.project_manager');
        // Check if project exists
        $exercise = $manager->getRepository()->find($exerciseId);
        if (!$exercise || !$manager->mayEdit($exercise)) {
            // no exercise or no right: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $form = $this->createForm('project', $exercise, array('exercise'=>true));
        
        if ($request->isMethod('POST')) {
            // form submitted
            $form->handleRequest($request);
        
            if ($form->isValid()) {
                $manager->saveExercise($exercise);
                if ($exerciseId == $session->get('projectid')) {
                    // current project has been updated: user menu has to be updated
                    $session->set('userMenuUpdateRequired', true);
                }
                $session->getFlashBag()->add('success', $this->get('translator')->trans('exercise.edited'));
                return $this->redirect($this->generateUrl('tangara_exercise_show', array('exerciseId' => $exerciseId)));                
            }
        }
        if (isset($form->attr)) {
            $form->attr = array_merge($form->attr, array('class'=>'form-content'));
        } else {
            $form->attr = array('class'=>'form-content');
        }
        return $this->renderContent('TangaraCoreBundle:Exercise:edit.html.twig', 'profile', array('form' => $form->createView(), 'title'=>'exercise.edition'));
    }
    
    public function selectAction($exerciseId, Request $request) {
        $session = $request->getSession();
        $manager = $this->get('tangara_core.project_manager');
        $exercise = $manager->getRepository()->find($exerciseId);
        if (!$exercise || !$manager->maySelect($exercise)) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $params = array();
        if ($exerciseId != $session->get('projectid')) {
            // update required:
            $session->set('projectid', $exerciseId);
            $params['updateUserMenu'] = true;
        }
        
        return $this->renderContent('TangaraCoreBundle:Exercise:select.html.twig', 'create', $params);
    }

    public function deleteAction($exerciseId, Request $request) {
        $session = $request->getSession();
        if ($exerciseId === false) {
            $exerciseId = $session->get('projectid');
        }
        if (!$exerciseId) {
            // no current project: we should not be here
            return $this->redirect($this->generateUrl( 'tangara_core_homepage'));
        }
        $manager = $this->get('tangara_core.project_manager');
        $exercise = $manager->getRepository()->find($exerciseId);
        if (!$exercise || !$manager->mayEdit($exercise)) {
            // no exercise or no right: we should not be here
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $manager->delete($exercise);
        $session->getFlashBag()->add('success', $this->get('translator')->trans('exercise.deleted'));
        if ($exerciseId == $session->get('projectid')) {
            // exercise was the currently selected project: remove it
            $session->remove('projectid');
            $session->set('update_required', true);
        }
        return $this->redirect($this->generateUrl('tangara_admin_exercises'));
    }
    
    
    
    
    public function listAction() {
        if (!$this->isUserLogged() || !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        $repository = $this->get('tangara_core.project_manager')->getRepository();
        $exercises = $repository->getOwnedExercises($this->getUser());
        return $this->renderContent('TangaraCoreBundle:Exercise:list.html.twig', 'project', array('exercises'=>$exercises));        
    }
    
    
}
