<?php

namespace Tangara\TangaraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Tangara\TangaraBundle\Form\ProjectType;
use Tangara\TangaraBundle\Entity\Document;
use Tangara\TangaraBundle\Entity\Project;
use Tangara\UserBundle\Entity\User;
use Tangara\UserBundle\Entity\Group;

class FileController extends Controller {

    public function fileAction(Project $project) {
        $user = $request->get('security.context')->getToken()->getUser();
        $user_id = $user->getId();
        
        $request = $this->getRequest();

        $document = new Document();
        $form = $this->createFormBuilder($document)
                ->add('file')
                ->getForm()
        ;
        $fs = new Filesystem();
        $project_user_path = "C:/Tangara/";
        $fs->mkdir($project_user_path);
        checkAction($user, $project);
        
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();
            
            

            //$file_uploaded = $request->get('file');
            $document->upload();
            //$fs->copy($file_uploaded, $project_user_path);
            $em->persist($document);
            $em->flush();

            //$ret = 'done ' . $file_uploaded ; 
            //return new \Symfony\Component\HttpFoundation\Response($ret);
        }

        return $this->render('TangaraTangaraBundle:Project:upload.html.twig', array(
                    'form' => $form->createView()
        ));
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

}
