<?php

namespace Tangara\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Tangara\ProjectBundle\Form\ProjectType;
use Tangara\ProjectBundle\Entity\Document;
use Tangara\ProjectBundle\Entity\Project;
use Tangara\UserBundle\Entity\User;
use Tangara\UserBundle\Entity\Group;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller {

    public function fileAction() {
        //$id = $request->get('security.context')->getToken()->getUser()->getId();
        $project_id = 23;
        $user_id = 2;
        checkAction($user_id, $project_id);

        $request = $this->getRequest();

        $document = new Document();
        $form = $this->createFormBuilder($document)
                ->add('name')
                ->add('file')
                ->getForm()
        ;

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();

            $document->upload($project_id);
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
            $fs = new Filesystem();
            $base_path = 'C:\tangara';
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

        if ($request) {
            $response = new JsonResponse();
            $response->setData(array(
                'files' => array("niveau1.tgr", "niveau2.tgr", "promeneur.tgr", "fin.tgr")
            ));
            return $response;
        }
    }

    public function getContentAction() {
        
    }

    public function removeFileAction() {
        
    }

    public function getTgrContentAction() {
        
    }

    public function getParseContentAction() {
        
    }

    public function getResourcesAction() {
        
    }

}
