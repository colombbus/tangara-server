<?php

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends TangaraController {

    public function indexAction() {
        return $this->render('TangaraCoreBundle:Admin:homepage.html.twig');
    }
    
    public function usersAction() {
        return $this->render('TangaraCoreBundle:Admin:users.html.twig');
    }
    
    // temp function to update storage 
    public function updateProjectAction($projectId) {
        // get managers
        $projectManager = $this->get('tangara_core.project_manager');
        $fileManager = $this->get('tangara_core.file_manager');
        $project = $projectManager->getRepository()->find($projectId);
        $user = $this->getUser();
        
        if (!$this->isUserLogged()) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        $auth = $this->get('security.context')->isGranted('ROLE_ADMIN');
        if (!$auth) {
            return $this->redirect($this->generateUrl('tangara_core_homepage'));
        }
        
        // handle resources
        $resources = $projectManager->getAllResources($project);
        $message = '';
        $mainResult = 'ok';
        foreach ($resources as $resource) {
            $storage = $resource->getStorageName();
            if (!isset($storage)) {
                // update storage
                $fileName = $resource->getName();
                $storageName = $projectManager->getNewStorageName($project);
                $resource->setStorageName($storageName);
                // rename file
                $projectPath = $projectManager->getProjectPath($project);
                $result = file_exists($projectPath."/".$fileName);
                if ($result) {
                    $result = rename($projectPath."/".$fileName, $projectPath."/".$storageName);
                }
                if (!$result) {
                    $message .= 'unable to copy file \''.$fileName.'\' to \''.$storageName.'\'<br>';
                    $mainResult = 'nok';
                }
                $fileManager->saveFile($resource);
            }
        }

        // handle programs
        $programs = $projectManager->getAllPrograms($project);
        foreach ($programs as $program) {
            $storage = $program->getStorageName();
            if (!isset($storage)) {
                // update storage
                $fileName = $program->getName();
                $storageName = $projectManager->getNewStorageName($project);
                $program->setStorageName($storageName);
                // rename file
                $projectPath = $projectManager->getProjectPath($project);
                $result = file_exists($projectPath."/".$fileName."_code");
                if ($result) {
                    $result = rename($projectPath."/".$fileName."_code", $projectPath."/".$storageName."_code");
                }
                if (!$result) {
                    $message .= 'unable to copy file \''.$fileName.'_code\' to \''.$storageName.'_code\'<br>';
                    $mainResult = 'nok';
                }
                $result = file_exists($projectPath."/".$fileName."_statements");
                if ($result) {
                    $result = rename($projectPath."/".$fileName."_statements", $projectPath."/".$storageName."_statements");
                }
                if (!$result) {
                    $message .= 'unable to copy file \''.$fileName.'_statements\' to \''.$storageName.'_statements\'<br>';
                    $mainResult = 'nok';
                }
                $fileManager->saveFile($program);
            }
        }
        
        return $this->renderContent('TangaraCoreBundle:Admin:result.html.twig', 'admin', array('result'=>$mainResult, 'message'=>$message));
    }
    
    
}
