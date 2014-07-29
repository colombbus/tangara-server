<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tangara\CoreBundle\Entity\Document;

class FileController extends Controller {

    /**
     * Checks if directory exists
     * 
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @param \Tangara\CoreBundle\Entity\User $user
     * @return true if directory exists
     */
    function checkDirectory($project, $user) {
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
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getResourcesAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $projectId = $session->get('projectid');

        //if ($request->isXmlHttpRequest()) {
        $projectList = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Document')
                ->findByOwnerProject($projectId);
        $files = null;
        foreach ($projectList as $prj) {
            $ext = pathinfo($prj->getPath(), PATHINFO_EXTENSION);
            if ($ext !== 'tgr')
                $files[] = $prj->getPath();
        }
        $response = new JsonResponse();

        if ($files)
            $response->setData(array('files' => $files));
        else
            $response->setData(array('files' => ''));

        return $response;
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

        foreach ($projectList as $prj) {
            $ext = pathinfo($prj->getPath(), PATHINFO_EXTENSION);
            if ($ext == 'tgr')
                $files[] = $prj->getPath();
        }
        $response = new JsonResponse();
        if ($files)
            $response->setData($files);
        else
            $response->setData(array(''));

        return $response;
    }

    /**
     * Get a content for a Tangara File given in a 'file' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
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
        //return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');
        if (!$request->isXmlHttpRequest())
            return new Response('XHR only...');

        if (!$auth) {
            $response = new JsonResponse();
            $response->setData(array('error' => 'unauthorized'));

            return $response;
        }
        $filename = $request->request->get('file');

        if ($filename) {
            $ownedFile = $this
                    ->get('tangara_core.project_manager')
                    ->isProjectFile($project, $filename);
            if (!$ownedFile) {
                $error = new JsonResponse();
                $error->setData(array('error' => 'unowned'));
                return $error;
            }

            if ($filename) {
                $filepath = $projectPath . '/' . $filename;

                $fs = new Filesystem();
                if ($fs->exists($filepath)) {
                    $response = new BinaryFileResponse($filepath);
                    $response->headers->set('Content-Type', 'text/plain');
                    return $response;
                } else {
                    return new FileNotFoundException();
                    //return new Response("file doesn't exist");
                }
            }
        }
    }
    /**
     * Remove from a project a file given in a 'file' field 
     * POST request 
     * Related current project is in 'projectid' field stored in session
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
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

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }

        if (!$auth) {
            return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');
        }

        $filename = $request->request->get('file');

        if ($filename) {
            $em = $this->getDoctrine()->getManager();
            $fileRepository = $em->getRepository('TangaraCoreBundle:Document');
            $f = $fileRepository->findOneByPath($filename);

            $em->remove($f);
            $em->flush();
            $filepath = $projectPath . '/' . $filename;

            $fs = new Filesystem();
            if ($fs->exists($filepath)) {
                $fs->remove($filepath);
                $response = new JsonResponse();
                return $response->setData(array('removed' => $filename));
            }
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

        $this->checkDirectory($project, $user);

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }

        if (!$auth) {
            return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');
        }

        $filename = $request->request->get('file');
        $everExistDocument = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Document')
                ->findOneByPath($filename);

        if ($everExistDocument) {
            $response = new JsonResponse();
            return $response->setData(array('error' => 'exists'));
        }
        if ($filename) {
            $em = $this->getDoctrine()->getManager();

            $document = new Document();
            $document->setOwnerProject($project);
            $document->setUploadDir($projectPath);
            $document->setPath($filename);

            $em->persist($document);
            $em->flush();
            $filepath = $projectPath . '/' . $filename;

            $fs = new Filesystem();
            if ($fs->exists($filepath)) {
                $response = new JsonResponse();
                return $response->setData(array('error' => 'exists'));
            } else {
                file_put_contents($filepath, LOCK_EX);
                $response = new JsonResponse();
                return $response->setData(array('created' => $filename));
            }
        }
    }

    public function setProgramContentAction() {
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

        $this->checkDirectory($project, $user);

        if (!$request->isXmlHttpRequest()) {
            return new Response('XHR only...');
        }

        if (!$auth) {
            $response = new JsonResponse();
            $response->setData(array('error' => 'unauthorized'));

            return $response;
        }
        $filename = $request->request->get('file');
        $content = $request->request->get('data');

        if ($filename) {
            $ownedFile = $this
                    ->get('tangara_core.project_manager')
                    ->isProjectFile($project, $filename);
            if (!$ownedFile) {
                $error = new JsonResponse();
                $error->setData(array('error' => 'unowned'));
                return $error;
            }
            $filepath = $projectPath . '/' . $filename;

            $fs = new Filesystem();
            if ($fs->exists($filepath)) {
                file_put_contents($filepath, $content, LOCK_EX);
                $response = new JsonResponse();
                $response->setData(array('modified' => $filename));
                return $response;
            } else {
                return new Response("file doesn't exist");
            }
        }
    }

}
