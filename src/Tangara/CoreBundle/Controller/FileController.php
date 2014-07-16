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

    public function testAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $projectPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        echo "path" . $projectPath;
    
        return new Response("ok");
    }

    public function checkAction($user, $project) {
        // Check if 
        $groupList = $user->getGroups();
        $projectGroup = $project->getGroup();
        
        if (in_array($projectGroup, $groupList))
                echo "granted !!!!";
        exit();

        if ($user)
            return false;
        else {
            return true;
            /* check if directory exists */
            $project_path = $base_path . "/" . $project_id;

            if (!$fs->exists($project_path)) {
                $fs->mkdir($project_path);
            }
        }
    }

    public function sendContentAction() {
        $request = $this->getRequest();
        //$id = $request->get('security.context')->getToken()->getUser()->getId();
        if ($request->isXmlHttpRequest()) {
            if ($request->query->get('userproject'))
                echo "USER PROJECT";

            if ($request->query->get('filename')) {
                $filename = $request->query->get('filename');
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
    }

    public function getFilesAction() {
        $request = $this->getRequest();
        //$id = $request->get('security.context')->getToken()->getUser()->getId();
        //TODO: if ($request->isXmlHttpRequest()) {
        if ($request) {
            $response = new JsonResponse();
            $response->setData(array(
                "bob.tgr", "pomme.tgr", "cubeQuest.tgr"
                //["bob.tgr", "pomme.tgr", "cubeQuest.tgr"]
            ));
            return $response;
        }
    }

    public function getContentAction(Project $project) {
        if ($request->query->get('filename'))
            echo "USER PROJECT";
    }

    public function removeFileAction(Project $project) {
        if ($request->query->get('filename'))
            echo "USER PROJECT";
    }

    public function getTgrContentAction(Project $project) {
        if ($request->query->get('filename'))
            echo "USER PROJECT";
    }

    public function getParseContentAction(Project $project) {
        if ($request->query->get('filename'))
            echo "USER PROJECT";
    }

    public function getResourcesAction(Project $project) {
        
    }

    public function getGrantedAction() {
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository('TangaraCoreBundle:Project');
        
        $query = $repository->findGranted();
        
        return $this->render('TangaraCoreBundle:Default:granted.html.twig', array(
                    'query' => $query
        ));
}
}
