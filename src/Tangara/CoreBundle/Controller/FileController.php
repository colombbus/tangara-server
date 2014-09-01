<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tangara\CoreBundle\Entity\Document;
use Tangara\CoreBundle\Entity\Project;

class FileController extends Controller {

    /**
     * Checks if directory exists
     * 
     * @param Project $project
     * @return true if directory exists
     */
    function checkDirectory($project) {
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
     * Get all resources included in a project
     * @param Project $project
     * @return JsonResponse
     */
    public function getResourcesAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $projectId = $session->get('projectid');

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }
        $projectList = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Document')
                ->findByOwnerProject($projectId);
        $files = null;
        foreach ($projectList as $prj) {
            $ext = pathinfo($prj->getPath(), PATHINFO_EXTENSION);
            if ($ext !== 'tgr') {
                $files[] = $prj->getPath();
            }
        }
        $jsonResponse = new JsonResponse();

        if ($files) {
            return $jsonResponse->setData(array('files' => $files));
        } else {
            return $jsonResponse->setData(array('files' => ''));
        }
    }

    /**
     * Get all tgr included in a project
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTangaraFilesAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $projectId = $session->get('projectid');

        $projectList = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Document')
                ->findByOwnerProject($projectId);
        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }
        
        $files = array();
        
        foreach ($projectList as $prj) {
            $ext = pathinfo($prj->getPath(), PATHINFO_EXTENSION);
            if ($ext == 'tgr') {
                $files[] = $prj->getPath();
            }
        }
        $jsonResponse = new JsonResponse();
        return $jsonResponse->setData($files);
    }

    /**
     * Get a content for a Tangara File given in a 'file' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function getProgramContentAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $projectId = $session->get('projectid');

        $project = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Project')
                ->findOneById($projectId);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user); //TODO GET PROJECT 
        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $projectId;

        $jsonResponse = new JsonResponse();
        $jsonError = new JsonResponse();

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }

        if (!$auth) {
            return $jsonError->setData(array('error' => 'unauthorized'));
        }
        $filename = $request->request->get('file');

        if (!$filename) {
            return $jsonError->setData(array('error' => 'no_filename_given'));
        }
        $ownedFile = $this->get('tangara_core.project_manager')
                ->isProjectFile($project, $filename);
        if (!$ownedFile) {
            return $jsonError->setData(array('error' => 'unowned'));
        }
        $path = $projectPath . '/' . $filename;

        $codePath = $projectPath . '/' . $filename . '_code';
        $statementsPath = $projectPath . '/' . $filename . '_statements';

        $fs = new Filesystem();
        if (!$fs->exists($codePath)) {
            return $jsonError->setData(array('error' => 'code_not_found'));
        }
        if (!$fs->exists($statementsPath)) {
            return $jsonError->setData(array('error' => 'statements_not_found'));
        }
        $content = new BinaryFileResponse($path);
        return $content;
//            } else {
//                return $jsonError->setData(array('error' => 'file_not_found'));
//            }
    }

    /**
     * Remove from a project a file given in a 'file' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return JsonResponse
     */
    public function removeFileAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $projectId = $session->get('projectid');

        $project = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Project')
                ->findOneById($projectId);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);
        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $projectId;

        $jsonResponse = new JsonResponse();
        $jsonError = new JsonResponse();

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }

        if (!$auth) {
            return $jsonError->setData(array('error' => 'unauthorized'));
        }

        $filename = $request->request->get('file');

        if (!$filename) {
            return $jsonError->setData(array('error' => 'no_filename_given'));
        }
        $em = $this->getDoctrine()->getManager();
        $fileRepository = $em->getRepository('TangaraCoreBundle:Document');
        $f = $fileRepository->findOneByPath($filename);

        $em->remove($f);
        $em->flush();
        $filepath = $projectPath . '/' . $filename;

        $fs = new Filesystem();
        if ($fs->exists($filepath)) {
            $fs->remove($filepath);
            return $jsonResponse->setData(array('removed' => $filename));
        } else {
            return $jsonError->setData(array('error' => 'file_not_found'));
        }
    }

    public function createAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $projectId = $session->get('projectid');

        $project = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Project')
                ->findOneById($projectId);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);
        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $projectId;

        $this->checkDirectory($project);

        $jsonResponse = new JsonResponse();
        $jsonError = new JsonResponse();

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }

        if (!$auth) {
            return $jsonError->setData(array('error' => 'unauthorized'));
        }
        
        $filename = $request->request->get('file');
        $everExistDocument = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Document')
                ->findOneByPath($filename);

        if ($everExistDocument) {
            return $jsonError->setData(array('error' => 'exists'));
        }
        if (!$filename) {
            return $jsonError->setData(array('error' => 'no_filename_given'));
        }

        $em = $this->getDoctrine()->getManager();

        $document = new Document();
        $document->setOwnerProject($project);
        $document->setUploadDir($projectPath);
        $document->setPath($filename);

        $em->persist($document);
        $em->flush();
        $filepath = $projectPath . '/' . $filename;

        $fs = new Filesystem();
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == 'tgr') {
            $codePath = $projectPath . '/' . $filename . '_code';
            $statementsPath = $projectPath . '/' . $filename . '_statements';
            file_put_contents($codePath, LOCK_EX);
            file_put_contents($statementsPath, LOCK_EX);
            return $jsonResponse->setData(array(
                        'code' => $filename . '_code',
                        'statements' => $filename . '_statements'
            ));
        }
        file_put_contents($filepath, LOCK_EX);
        return $jsonResponse->setData(array('created' => $filename));
    }

    public function setProgramContentAction() {
        $request = $this->getRequest();
        $projectId = $request->getSession()->get('projectid');

        $project = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Project')
                ->findOneById($projectId);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);
        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $projectId;

        $this->checkDirectory($project);

        $jsonResponse = new JsonResponse();
        $jsonError = new JsonResponse();

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }
        if (!$auth) {
            return $jsonError->setData(array('error' => 'unauthorized'));
        }
        $filename = $request->request->get('file');
        $codeContent = $request->request->get('code');
        $statementsContent = $request->request->get('statements');

        if (!$filename) {
            return $jsonError->setData(array('error' => 'no_filename_given'));
        }
        $ownedFile = $this
                ->get('tangara_core.project_manager')
                ->isProjectFile($project, $filename);
        if (!$ownedFile) {
            return $jsonError->setData(array('error' => 'unowned'));
        }
        $filepath = $projectPath . '/' . $filename;
        $codePath = $projectPath . '/' . $filename . '_code';
        $statementsPath = $projectPath . '/' . $filename . '_statements';

        $fs = new Filesystem();
        if (!$fs->exists($codePath)) {
            return $jsonError->setData(array('error' => 'code_not_found'));
        }
        if (!$fs->exists($statementsPath)) {
            return $jsonError->setData(array('error' => 'statements_not_found'));
        }
        file_put_contents($codePath, $codeContent, LOCK_EX);
        file_put_contents($statementsPath, $statementsContent, LOCK_EX);
        return $jsonResponse->setData(array('modified' => $filename));
    }

}
