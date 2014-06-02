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

class FileController extends Controller {

    public function fileAction() {
        //$id = $request->get('security.context')->getToken()->getUser()->getId();
        $project_id = 23;
        $user_id = 2;
        $base_path = 'C:\tangara';
        $project_user_path = $base_path . "/" . $user_id;
        $project_path = $base_path . "/" . $project_id;

        $request = $this->getRequest();

        $document = new Document();
        $form = $this->createFormBuilder($document)
                ->add('name')
                ->add('file')
                ->getForm()
        ;
        $fs = new Filesystem();

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            $em = $this->getDoctrine()->getManager();

            if (!$fs->exists($project_user_path)) {
                $fs->mkdir($project_user_path); // perso projects
            }

            if (!$fs->exists($project_path)) {
                $fs->mkdir($project_path); // perso projects
            }
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
        
    public function dataAction() {
        $request = $this->getRequest();
        //$id = $request->get('security.context')->getToken()->getUser()->getId();
        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setData(array(
                'projectId' => 121,
                'projectURL' => "http://apps.colombbus.org/tangara_ui/project/121",
                'screen' => array('width' => 1024,
                    'height' => 768),
                'files' => array("niveau1.tgr","niveau2.tgr", "promeneur.tgr", "fin.tgr")
            ));
            return $response;
        }
    }

    public function getFilesAction() {
        $request = $this->getRequest();
        //$id = $request->get('security.context')->getToken()->getUser()->getId();

        if ($request) {
            $response = new JsonResponse();
            $response->setData(array(
                'files' => array("niveau1.tgr","niveau2.tgr", "promeneur.tgr", "fin.tgr")
            ));
            return $response;
        }
    }
}
