<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tangara\CoreBundle\Entity\File;
use Tangara\CoreBundle\Entity\Project;
use stdClass;

class FileController extends Controller {

    /**
     * Checks if directory exists
     * 
     * @param Project $project
     * @return true if directory exists
     */
    protected function checkDirectory($project) {
        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $project->getId();
        $fs = new Filesystem();

        if (!$fs->exists($projectPath)) {
            $fs->mkdir($projectPath);
            return false;
        }
        return true;
    }

    /**
     * Sanity checks: checks if request is XML ; checks if required fields are provided ; 
     * checks if project id set ; checks if user can access current project
     * 
     * @param Project $project
     * @return true if directory exists
     */
    protected function checkEnvironment($fields) {
        $env = new stdClass();
        $request = $this->getRequest();
        // Check if request is xml
        if (!$request->isXmlHttpRequest()) {
            $env->error = "not_xml_request";
            return $env;
        }
        
        // Check if required fields are provided
        foreach($fields as $field) {
            $value = $request->request->get($field);
            if (!$value) {
                $env->error = "missing_field_$field";
                return $env;
            }
        }
        
        // Check if project id set
        $session = $request->getSession();
        $projectId = $session->get('projectid');
        if (!$projectId) {
            $env->error = "project_not_set";
            return $env;
        }
        $env->projectId = $projectId;
        
        // Check user
        $user = $this->container->get('security.context')->getToken()->getUser();
        $env->user = $user;
        
        // Check if project exists
        $project = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Project')
                ->findOneById($projectId);
        if (!$project) {
            $env->error = "wrong_project_id";
            return $env;
        }
        $env->project = $project;
        
        // Check project access by user
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);
        if (!$auth) {
            $env->error = "unauthorized_access";
            return $env;
        }
        
        // Check project directory
        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $project->getId();
        $fs = new Filesystem();
        if (!$fs->exists($projectPath)) {
            $fs->mkdir($projectPath);
        }
        $env->projectPath = $projectPath;
        
        return $env;
    }
    
       
    /**
     * Get all resources included in a project
     * 
     * @return JsonResponse
     */
    public function getResourcesAction() {
        $env = $this->checkEnvironment(array());
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        $resources = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:File')
                ->getAllProjectResources($env->project);
        
        $files = array();
        foreach ($resources as $resource) {
            $files[] = $resource->getPath();
        }
 
        return $jsonResponse->setData(array('resources' => $files));
    }

    /**
     * Get all programs included in a project
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProgramsAction() {
        $env = $this->checkEnvironment(array());
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        $programs = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:File')
                ->getAllProjectPrograms($env->project);
        
        $files = array();
        foreach ($programs as $program) {
            $files[] = $program->getPath();
        }
 
        return $jsonResponse->setData(array('programs' => $files));
    }

    protected function getProgramContent($statements = false) {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        $programName = $this->getRequest()->request->get('name');
        
        $existing = $this->get('tangara_core.project_manager')->isProjectFile($env->project, $programName, true);
        if (!$existing) {
            return $jsonResponse->setData(array('error' => 'program_not_found'));
        }

        if ($statements) {
            $path = $env->projectPath . "/${programName}_statements";
            $dataName = 'statements';
        } else {
            $path = $env->projectPath . "/${programName}_code";
            $dataName = 'code';
        }

        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            return $jsonResponse->setData(array('error' => "${dataName}_not_found"));
        }
        
        $content = file_get_contents($path);
        
        if (!$content) {
            return $jsonResponse->setData(array('error' => "read_error"));
        }
        
        return $jsonResponse->setData(array($dataName => $content)); 
    }
    
    /**
     * Get code for a program given a 'name' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function getProgramCodeAction() {
        return $this->getProgramContent(false);
    }
    
    /**
     * Get statements for a program given a 'name' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function getProgramStatementsAction() {
        return $this->getProgramContent(true);
    }
    
    /**
     * Get a resource file given a 'name' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function getResourceAction() {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        $resourceName = $this->getRequest()->request->get('name');
        
        $existing = $this->get('tangara_core.project_manager')->isProjectFile($env->project, $programName, false);
        if (!$existing) {
            return $jsonResponse->setData(array('error' => 'resource_not_found'));
        }

        $path = $env->projectPath . "/$resourceName";

        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            return $jsonResponse->setData(array('error' => "resource_not_found"));
        }
        
        return new BinaryFileResponse($path);
    }
    
    /**
     * Remove a program from the current project, given in a 'name' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function removeProgramAction() {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        
        $programName = $this->getRequest()->request->get('name');

        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository('TangaraCoreBundle:File');
        $program = $repository->getProjectProgram($env->projectId, $programName);
        
        if (!$program) {
            return $jsonResponse->setData(array('error' => "program_not_found"));
        }

        $manager->remove($program);
        $manager->flush();
        
        $codePath = $env->projectPath . "/${programName}_code";
        $statementsPath = $env->projectPath . "/${programName}_statements";

        $fs = new Filesystem();
        if ($fs->exists($codePath)) {
            $fs->remove($codePath);
        }
        if ($fs->exists($statementsPath)) {
            $fs->remove($statementsPath);
        }
        
        return $jsonResponse->setData(array('removed'=>$programName));
    }
    
    /**
     * Remove a resource file from the current project, given in a 'name' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function removeResourceAction() {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }        
        $resourceName = $this->getRequest()->request->get('name');

        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository('TangaraCoreBundle:File');
        $resource = $repository->getProjectResource($env->projectId, $resourceName);
        
        if (!$resource) {
            return $jsonResponse->setData(array('error' => "resource_not_found"));
        }

        $manager->remove($resource);
        $manager->flush();
        
        $path = $env->projectPath . "/$resourceName";

        $fs = new Filesystem();
        if ($fs->exists($path)) {
            $fs->remove($path);
        }
        
        return $jsonResponse->setData(array('removed'=>$resourceName));
    }

    /**
     * Create a program in the current project, from the 'name' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function createProgramAction() {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }        
        $programName = $this->getRequest()->request->get('name');
        
        // Check if programName already exists
        $existing = $this->get('tangara_core.project_manager')->isProjectFile($env->project, $programName, true);
        if ($existing) {
            return $jsonResponse->setData(array('error' => 'program_already_exists'));
        }
        
        // Create new file
        $manager = $this->getDoctrine()->getManager();
        $file = new File();
        $file->setProject($env->project);
        $file->setPath($programName);
        $file->setProgram(true);
        $manager->persist($file);
        $manager->flush();
        
        return $jsonResponse->setData(array('created' => $programName));
    }
    
    /**
     * Create a resource in the current project, from the 'name' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function createResourceAction() {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }        
        $resourceName = $this->getRequest()->request->get('name');
        
        // Check if programName already exists
        $existing = $this->get('tangara_core.project_manager')->isProjectFile($env->project, $resourceName, false);
        if ($existing) {
            return $jsonResponse->setData(array('error' => 'resource_already_exists'));
        }
        
        // Create new file
        $manager = $this->getDoctrine()->getManager();
        $file = new File();
        $file->setOwnerProject($env->project);
        $file->setUploadDir($env->projectPath);
        $file->setPath($resourceName);
        $file->setProgram(false);
        $manager->persist($file);
        $manager->flush();
        
        return $jsonResponse->setData(array('created' => $resourceName));
    }

    /**
     * Set the content of a given program 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function setProgramContentAction() {
        $env = $this->checkEnvironment(array('name','code','statements'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }        
        $programName = $this->getRequest()->request->get('name');
        $code = $this->getRequest()->request->get('code');
        $statements = $this->getRequest()->request->get('statements');
        
        // Check if program actually exists
        $existing = $this->get('tangara_core.project_manager')->isProjectFile($env->project, $programName, true);
        if (!$existing) {
            return $jsonResponse->setData(array('error' => 'program_not_found'));
        }

        $codePath = $env->projectPath . "/${programName}_code";
        $statementsPath = $env->projectPath . "/${programName}_statements";

        file_put_contents($codePath, $code, LOCK_EX);
        file_put_contents($statementsPath, $statements, LOCK_EX);
        return $jsonResponse->setData(array('updated' => $programName));
    }

     /**
     * Rename a given program 
     * POST request 
     * Related project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function renameProgramAction() {
        $env = $this->checkEnvironment(array('name','new'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }        
        $programName = $this->getRequest()->request->get('name');
        $newName = $this->getRequest()->request->get('new');
        
        // Get current program and check it actually exists
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository('TangaraCoreBundle:File');
        $program = $repository->getProjectProgram($env->projectId, $programName);
        if (!$program) {
            return $jsonResponse->setData(array('error' => "program_not_found"));
        }

        // Check new name does not already exist
        $existing = $this->get('tangara_core.project_manager')->isProjectFile($env->project, $newName, true);
        if ($existing) {
            return $jsonResponse->setData(array('error' => 'program_already_exists'));
        }

        // Set new name
        $program->setPath($newName);
        $manager->flush();

        // Change file names
        $oldCodePath = $env->projectPath . "/${programName}_code";
        $newCodePath = $env->projectPath . "/${newName}_code";
        $oldStatementsPath = $env->projectPath . "/${programName}_statements";
        $newStatementsPath = $env->projectPath . "/${newName}_statements";

        $fs = new Filesystem();
        if ($fs->exists($oldCodePath)) {
            rename($oldCodePath, $newCodePath);
        }
        if ($fs->exists($oldStatementsPath)) {
            rename($oldStatementsPath, $newStatementsPath);
        }
        
        return $jsonResponse->setData(array('updated' => $newName));
    }
    
}
