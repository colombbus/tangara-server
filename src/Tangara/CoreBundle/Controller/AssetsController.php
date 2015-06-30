<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tangara\CoreBundle\Entity\Project;
use stdClass;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

class AssetsController extends Controller {

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
    protected function checkEnvironment($fields, $xmlCheck = true, $projectId = false) {
        $env = new stdClass();
        $request = $this->getRequest();
        
        if ($xmlCheck) {
            // Check if request is xml
            if (!$request->isXmlHttpRequest()) {
                $env->error = "not_xml_request";
                return $env;
            }
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
        if (!$projectId) {
            $projectId = $request->request->get("project_id");
        }
        if (!$projectId) {
            $session = $request->getSession();
            $projectId = $session->get('projectid');
            if (!$projectId) {
                $env->error = "project_not_set";
                return $env;
            }
        }
        $env->projectId = $projectId;
        
        // Check user
        $context = $this->container->get('security.context');
        $user = $context->getToken()->getUser();
        $env->user = $user;
        
        // Check if logged
        $env->authenticated = $context->isGranted('IS_AUTHENTICATED_FULLY');
        
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
        $manager = $this->get('tangara_core.project_manager');
        if (!$manager->mayExecute($project)) {
            $env->error = "unauthorized_access";
            return $env;
        }
        
        $env->editionAllowed = $manager->mayContribute($project);

        // Get project directory
        $env->projectPath = $this->get('tangara_core.project_manager')->getProjectPath($project);
        
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

        $resources = $this->get('tangara_core.project_manager')->getAllResources($env->project);

        $files = array();
        foreach ($resources as $resource) {
            $files[$resource->getName()] = array('type'=>$resource->getType(), 'version'=>$resource->getVersion(), 'extension'=>$resource->getExtension(), 'base-name'=>$resource->getBaseName());
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
        
        $programs = $this->get('tangara_core.project_manager')->getAllPrograms($env->project);
        
        $files = array();
        foreach ($programs as $program) {
            $files[] = $program->getName();
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
        
        $program = $this->get('tangara_core.project_manager')->getProjectFile($env->project, $programName, true);
        if ($program === false) {
            return $jsonResponse->setData(array('error' => 'program_not_found'));
        }

        $filePath = $this->get('tangara_core.file_manager')->getFilePath($program);
        
        if ($statements) {
            $path = $env->projectPath . "/${filePath}_statements";
            $dataName = 'statements';
        } else {
            $path = $env->projectPath . "/${filePath}_code";
            $dataName = 'code';
        }

        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            //return $jsonResponse->setData(array('error' => "${dataName}_not_found"));
            // file does not exist: empty content
            $content = '';
        } else {
            $content = file_get_contents($path);
        }
        
        if ($content === false) {
            return $jsonResponse->setData(array('error' => "read_error"));
        }
        if ($statements) {
            $content = json_decode($content);
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
    public function getResourceAction($projectId, $version,$name) {
        $env = $this->checkEnvironment(array(), false, $projectId);
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        
        // check that resource exists
        $resource = $this->get('tangara_core.project_manager')->getProjectFile($env->project, $name, false);
        if ($resource === false) {
            return $jsonResponse->setData(array('error' => 'resource_not_found'));
        }

        // check that version number is correct
        $currentVersion = $resource->getVersion();
        if (is_int($version) || $version != $currentVersion) {
            // version number incorrect: send redirect response
            if ($projectId !== false) {
                return $this->redirect($this->generateUrl( 'tangara_tangarajs_get_resource_1', array('name'=>$name, 'projectId'=>$projectId, 'version'=>$currentVersion) ));
            } else {
                return $this->redirect($this->generateUrl( 'tangara_tangarajs_get_resource_2', array('name'=>$name, 'version'=>$currentVersion) ));
            }
        }
        
        $path = $env->projectPath."/".$this->get('tangara_core.file_manager')->getFilePath($resource);

        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            return new Response();
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
        
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $programName = $this->getRequest()->request->get('name');

        
        $pManager = $this->get('tangara_core.project_manager');
        // get program
        $manager = $this->get('tangara_core.file_manager');
        $repository = $manager->getRepository();
        $program = $repository->getProjectProgram($env->projectId, $programName);
        if (!$program) {
            return $jsonResponse->setData(array('error' => "program_not_found"));
        }
        
        // Remove program
        $pManager->removeFile($env->project, $program);

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
        
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $resourceName = $this->getRequest()->request->get('name');
        
        // Get project
        $pManager = $this->get('tangara_core.project_manager');

        // Get resource
        $manager = $this->get('tangara_core.file_manager');
        $repository = $manager->getRepository();
        $resource = $repository->getProjectResource($env->projectId, $resourceName);
        if (!$resource) {
            return $jsonResponse->setData(array('error' => "resource_not_found"));
        }
        
        // Remove resource
        $pManager->removeFile($env->project, $resource);
        
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
        
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $programName = $this->getRequest()->request->get('name');
        
        // Check if programName already exists
        $manager = $this->get('tangara_core.project_manager');
        $program = $manager->createFile($env->project, $programName, true);
        if ($program === false) {
            return $jsonResponse->setData(array('error' => 'program_already_exists'));
        }
        
        return $jsonResponse->setData(array('created' => $programName));
    }
    
    /**
     * Add a resource in the current project, from the 'file' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function addResourceAction() {
        $env = $this->checkEnvironment(array());
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }

        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $files = $this->getRequest()->files->get('resources');
        if (!isset($files)) {
            return $jsonResponse->setData(array('error' => 'no_resource_provided'));
        }
        $manager = $this->get('tangara_core.file_manager');
        $created = array();
        foreach ($files as $uploadedFile) {
            $name = $uploadedFile->getClientOriginalName();
            $check = $manager->checkResource($uploadedFile);
            if ($check !== true) {
                // an error occured
                return $jsonResponse->setData(array('error' => array('message'=>$check, 'name'=>$name)));
            }
            
            // Get file type and check it
            $type = $manager->getResourceType($uploadedFile);
            if ($type === false) {
                return $jsonResponse->setData(array('error' => 'bad_resource_type'));
            }
            // Try to get file extension
            $parts = explode(".", $name);
            if (sizeof($parts) <= 1) {
                // no extension found
                return $jsonResponse->setData(array('error' => 'bad_resource_type'));
            }
            $extension = end($parts);
            $baseName = substr($name, 0, -(strlen($extension)+1));
           
            // Get new file
            $file = $this->get('tangara_core.project_manager')->createFile($env->project, $name, false);
            if ($file === false) {
                // file name already in use
                return $jsonResponse->setData(array('error' => array('message'=>'resource_already_exists', 'name'=>$name)));
            }

            $file->setType($type);
            $file->setExtension($extension);
            $file->setBaseName($baseName);
            $uploadedFile->move($env->projectPath, $file->getStorageName());
            $manager->persistAndFlush($file);
            $created[] = array('name'=>$name, 'data' => array('type'=> $type, 'extension'=>$extension, 'base-name'=>$baseName, 'version'=>$file->getVersion()));
        }
        return $jsonResponse->setData(array('created' => $created));
    }

    /**
     * Set the content of a given program 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function setProgramContentAction() {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $programName = $this->getRequest()->request->get('name');
        $code = $this->getRequest()->request->get('code');
        $statements = $this->getRequest()->request->get('statements');
        
        // Get program
        $manager = $this->get('tangara_core.file_manager');
        $repository = $manager->getRepository();
        $program = $repository->getProjectProgram($env->projectId, $programName);
        if (!$program) {
            return $jsonResponse->setData(array('error' => "program_not_found"));
        }

        // Update content
        $pManager = $this->get('tangara_core.project_manager');
        $pManager->updateProgram($env->project, $program, $code, $statements);

        return $jsonResponse->setData(array('updated' => $programName));
    }

    /**
     * Set the content of a given resource
     * POST request 
     * 
     * @return JsonResponse
     */
    public function setResourceContentAction() {
        $env = $this->checkEnvironment(array('name','data'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $resourceName = $this->getRequest()->request->get('name');

        $pManager = $this->get('tangara_core.project_manager');
        
        // Get resource
        $manager = $this->get('tangara_core.file_manager');
        $repository = $manager->getRepository();
        $resource = $repository->getProjectResource($env->projectId, $resourceName);
        if (!$resource) {
            return $jsonResponse->setData(array('error' => "resource_not_found"));
        }

        $data = $this->getRequest()->request->get('data');
        if ($resource->getType() === "image") {
            // handle image data
            // remove header (get only image data)
            $pos = strpos($data, ',');
            if ($pos === false) {
                return $jsonResponse->setData(array('error' => "malformed_data"));
            }
            $data = substr($data, $pos+1);
            // base 64 decode
            $data = base64_decode($data);

            // Check extension
            if ($resource->getExtension() !== "png") {
                // name will change: check that new resource name does not exist
                $newName = $resource->getBaseName()."."."png";
                $existing = $repository->getProjectResource($env->projectId, $newName);
                if ($existing) {
                    // check if existing is deleted
                    if (!$existing->getDeleted()) {
                        return $jsonResponse->setData(array('error' => 'resource_already_exists'));
                    }
                    $resource->setVersion($existing->getVersion());
                    $pManager->removeFile($env->project, $existing, true);
                }

                // set new name and extension
                $resource->setName($newName);
                $resource->setExtension("png");
            }
        }
        
        $path = $env->projectPath . "/". $manager->getFilePath($resource);
        
        $result = file_put_contents($path, $data);
        
        if ($result === false) {
            return $jsonResponse->setData(array('error' => "write_error"));
        }
        
        $newVersion = $resource->getVersion()+1;
        $resource->setVersion($newVersion);
        $manager->saveFile($resource);

        return $jsonResponse->setData(array('updated' => $resource->getName(), 'data' => array('type'=> $resource->getType(), 'extension'=>$resource->getExtension(), 'base-name'=>$resource->getBaseName(), 'version'=>$newVersion)));
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
        
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $programName = $this->getRequest()->request->get('name');
        $newName = $this->getRequest()->request->get('new');
        
        // Get current program and check it actually exists
        $manager = $this->get('tangara_core.file_manager');
        $repository = $manager->getRepository();
        $program = $repository->getProjectProgram($env->projectId, $programName);
        if (!$program) {
            return $jsonResponse->setData(array('error' => "program_not_found"));
        }
        
        // Check that new name is not empty
        if (strlen(trim($newName))==0) {
            return $jsonResponse->setData(array('error' => 'empty_name'));
        }

        // Check new name does not already exist
        $existing = $this->get('tangara_core.project_manager')->isProjectFile($env->project, $newName, true);
        if ($existing) {
            return $jsonResponse->setData(array('error' => 'program_already_exists'));
        }

        // Set new name
        $program->setName($newName);
        $manager->saveFile($program);

        return $jsonResponse->setData(array('updated' => $newName));
    }
    
     /**
     * Rename a given resource
     * POST request 
     * 
     * @return JsonResponse
     */
    public function renameResourceAction() {
        $env = $this->checkEnvironment(array('name','new'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }

        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $resourceName = $this->getRequest()->request->get('name');
        $newBaseName = $this->getRequest()->request->get('new');
        
        $pManager = $this->get('tangara_core.project_manager');
        
        // Get current resource and check it actually exists
        $manager = $this->get('tangara_core.file_manager');
        $repository = $manager->getRepository();
        $resource = $repository->getProjectResource($env->projectId, $resourceName);
        if ($resource === false) {
            return $jsonResponse->setData(array('error' => "resource_not_found"));
        }

        // Check that new name is not empty
        if (strlen(trim($newBaseName))==0) {
            return $jsonResponse->setData(array('error' => 'empty_name'));
        }
        
        // Check new name does not already exist
        $newName = $newBaseName.".".$resource->getExtension();
        $newResource = $repository->getProjectResource($env->projectId, $newName);
        if ($newResource) {
            // check if previous resource was deleted
            if (!$newResource->getDeleted()) {
                return $jsonResponse->setData(array('error' => 'resource_already_exists'));
            }
            // increase resource version number
            $resource->setVersion($newResource->getVersion()+1);
            // actually delete previous resource
            $pManager->removeFile($env->project, $newResource, true);
        }

        // Set new name and new baseName
        $resource->setName($newName);
        $resource->setBaseName($newBaseName);
        $manager->saveFile($resource);

        return $jsonResponse->setData(array('updated' => $newName));
    }
    
     /**
     * Duplicate a given resource
     * POST request 
     * 
     * @return JsonResponse
     */
    public function duplicateResourceAction() {
        $env = $this->checkEnvironment(array('name'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }
        
        $resourceName = $this->getRequest()->request->get('name');
        
        // Get current resource and check it actually exists
        $manager = $this->get('tangara_core.project_manager');
        $resource = $manager->getProjectFile($env->project, $resourceName, false);
        if (!$resource) {
            return $jsonResponse->setData(array('error' => "resource_not_found"));
        }

        $baseName = $resource->getBaseName();
        $extension = $resource->getExtension();
        $newIndex = 1;
        
        $newName = "";
        $newBaseName = "";
        do {
            $newBaseName = $baseName."_".$newIndex;
            $newName = $newBaseName.".".$extension;
            
            // check if new resource already exists
            $copy = $manager->createFile($env->project, $newName, false);
            $newIndex++;
        } while ($copy === false);
        
        $copy->setBaseName($newBaseName);
        $copy->setExtension($extension);
        $copy->setType($resource->getType());
        $fileManager = $this->get('tangara_core.file_manager');
        $fileManager->saveFile($copy);
        
        // Copy file
        $originalPath = $env->projectPath . "/".$fileManager->getFilePath($resource);
        $copyPath = $env->projectPath . "/".$fileManager->getFilePath($copy);;
        $fs = new Filesystem();
        try {
            $fs->copy($originalPath, $copyPath);
        } catch (FileNotFoundException $ex) {
            return $jsonResponse->setData(array('error' => 'file_not_found'));
        }  catch (IOException $ex) {
            return $jsonResponse->setData(array('error' => 'io_error'));
        }
        return $jsonResponse->setData(array('created' => $newName, 'data'=> array('type'=> $copy->getType(), 'extension'=>$extension, 'base-name'=>$newBaseName, 'version'=>$copy->getVersion())));
    }
    
    /**
     * Create a new image resource
     * POST request 
     * 
     * @return JsonResponse
     */
    public function createResourceAction() {
        $env = $this->checkEnvironment(array('name','data'));
        $jsonResponse = new JsonResponse();
        if (isset($env->error)) {
            return $jsonResponse->setData(array('error' => $env->error));
        }
        if (!$env->editionAllowed) {
            return $jsonResponse->setData(array('error' => 'edition_forbidden'));
        }

        $baseName = $this->getRequest()->request->get('name');
        $extension = "png";
        $name = $baseName.".".$extension;
        
        // Handle data
        $data = $this->getRequest()->request->get('data');
        // remove header (get only image data)
        $pos = strpos($data, ',');
        if ($pos === false) {
            return $jsonResponse->setData(array('error' => "malformed_data"));
        }
        $data = substr($data, $pos+1);
        // base 64 decode
        $data = base64_decode($data);
        
        // Create resource
        $resource = $this->get('tangara_core.project_manager')->createFile($env->project, $name, false);
        if ($resource === false) {
            // file name already in use
            return $jsonResponse->setData(array('error' => 'resource_already_exists'));
        }        
        
        $fileManager = $this->get('tangara_core.file_manager');
        $path = $env->projectPath . "/". $fileManager->getFilePath($resource);

        $result = file_put_contents($path, $data);
        
        if ($result === false) {
            return $jsonResponse->setData(array('error' => "write_error"));
        }

        // Set resource additional information
        $resource->setBaseName($baseName);
        $resource->setExtension($extension);
        $resource->setType($fileManager->getResourceTypeFromMIME("image/png"));
        $fileManager->saveFile($resource);
        
        return $jsonResponse->setData(array('created' => $name, 'data' => array('type'=> $resource->getType(), 'extension'=>$extension, 'base-name'=>$baseName, 'version'=>$resource->getVersion())));
    }    
    
    
}
