<?php

namespace Tangara\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class archiveController extends Controller {

    public function localeAction() {
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if ($request) {
            $response = new JsonResponse();
            $response->setData(array(
                'locale' => $locale));
        }
    }
    public function getTgrAction() {
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if ($request) {
            $file = 'C:/Bin/cmd_aliases.txt';
            $response = new BinaryFileResponse($file);

            return $response;
        }
    }
    public function getContentTgrAction() {
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if ($request) {
            $file = 'C:/Bin/cmd_aliases.txt';
            $response = new BinaryFileResponse($file);
            
            return $response;
        }
    }

    public function fileAction() {
        //$user_id = $request->get('security.context')->getToken()->getUser()->getId();
        $project_id = 23;
        $user_id = 2;
        checkAction($user_id, $project_id);


        $request = $this->getRequest();

        $document = new Document();
        $form = $this->createFormBuilder($document)
                ->add('file')
                ->getForm()
        ;

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();

            $document->upload();
            //$file_uploaded = $request->get('file');
            //$fs->copy($file_uploaded, $project_user_path);
            $em->persist($document);
            $em->flush();

            //$ret = 'done ' . $file_uploaded ; 
            //return new \Symfony\Component\HttpFoundation\Response($ret);
        }

        return $this->render('TangaraProjectBundle:Default:upload.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    public function checkAction($user, $project) {
        /* obtenir la liste des groupes de l'utilisateur */

        /* obtenir le groupe du projet */

        /* le groupe  */

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
        $repository = $manager->getRepository('TangaraProjectBundle:Project');
        
        $query = $repository->findGranted();
        
        return $this->render('TangaraProjectBundle:Default:granted.html.twig', array(
                    'query' => $query
        ));
    }
}
