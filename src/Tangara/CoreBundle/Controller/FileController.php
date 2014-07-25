<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tangara\CoreBundle\Form\ProjectType;
use Tangara\CoreBundle\Entity\Document;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\User;
use Tangara\CoreBundle\Entity\Group;

class FileController extends Controller {

    function check($user, $project) {
        $uploadPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $projectPath = $uploadPath . '/' . $project->getId();

        if (!$fs->exists($projectPath)) {
            $fs->mkdir($projectPath);
        }
    }

    public function testAction(Project $project) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $projectPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $tangaraPath = $this->container->getParameter('tangara_core.settings.directory.tangarajs');

        echo "Uploadr path " . $projectPath . "<br/>";
        echo "Tangara path " . $tangaraPath . "<br/>";


        $this->get('session')->getFlashBag()->add(
                'notice', 'Vos changements ont été sauvegardés!'
        );

        return $this->render('TangaraCoreBundle::test.html.twig');
    }

    public function sendContentAction($cat, $project) {
        $request = $this->getRequest();
        //$user = $request->get('security.context')->getToken()->getUser()->getId();

        if ($request->query->get('sendfile'))
            echo "USER PROJECT";

        //$filename = $request->query->get('wanted');
        //$toSend = $request->request->get('filename');

        if ($filename) {
            $project_id = 23;
            $user_id = 2;
            $base_path = 'C:\tangara';
            $project_user_path = $base_path . "/" . $user_id;
            $project_path = $base_path . "/" . $project_id;
            $filepath = $project_path . "/" . $filename;
            $fs = new Filesystem();

            if ($fs->exists($filepath))
                return new BinaryFileResponse($filepath);
        }
    }

    /**
     * Get all resources included in a project
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getResourcesAction($cat, Project $project) {
        $request = $this->getRequest();
        // check($user, $project);
        //if ($request->isXmlHttpRequest()) {
        $projectList = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Document')
                ->findByOwnerProject($project->getId());

        foreach ($projectList as $prj) {
            $ext = pathinfo($prj->getPath(), PATHINFO_EXTENSION);
            if ($ext != 'tgr')
                $files[] = $prj->getPath();
        }
        $response = new JsonResponse();
        $response->setData($files);

        return $response;
    }

    /**
     * Get all tgr included in a project
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTangaraFilesAction($cat, Project $project) {
        $request = $this->getRequest();
        // check($user, $project);
        //if ($request->isXmlHttpRequest()) {
        $projectList = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Document')
                ->findByOwnerProject($project->getId());

        foreach ($projectList as $prj) {
            $ext = pathinfo($prj->getPath(), PATHINFO_EXTENSION);
            if ($ext == 'tgr')
                $files[] = $prj->getPath();
        }
        $response = new JsonResponse();
        $response->setData($files);

        return $response;
    }
    //getContentAction
    public function getFilesAction($cat, Project $project) {
        $request = $this->getRequest();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);
        //return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');

        if (!$request->isXmlHttpRequest())
            return new Response('XHR only...');

        if (!$auth) {
            $response = new JsonResponse();
            $response->setData(array('error' => 'unauthorized'));

            return $response;
        }
        $file = $request->query->get('filename');

        if ($file) {
            $ownedFile = $this
                    ->get('tangara_core.project_manager')
                    ->isProjectFile($project, $file);
            if (!$ownedFile) {
                $error = new JsonResponse();
                $error->setData(array('error' => 'unowned'));
                return $error;
            }

            $response = new JsonResponse();
            $response->setData(array('filecontent' => $file)); //TODO $file

            return $response;
        }

        return new Response("request");
    }

    public function removeFileAction(Project $project) {
        $request = $this->getRequest();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);

        if (!$auth) {
            return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');
        }

        $fileName = null;

        if ($request->query->get('removedfile')) {
            $fileName = $request->query->get('removedfile');
        }

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            //verifie si le fichier existe, si vrai
            if ($fileName) {
                $em = $this->getDoctrine()->getManager();
                $fileRepository = $em->getRepository('TangaraCoreBundle:Document');

                $file = $fileRepository->findByPath($fileName);

                $em->remove($file);
                $em->flush();
            }
        }
    }

    public function getTgrContentAction(Project $project) {
        $request = $this->getRequest();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);

        if (!$auth) {
            return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');
        }

        if ($request->query->get('tgrfile'))
            echo "USER PROJECT";
    }

    public function getParseContentAction(Project $project) {
        $request = $this->getRequest();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);

        if (!$auth) {
            return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');
        }

        if ($request->query->get('parsefile'))
            echo "USER PROJECT";
    }

}
