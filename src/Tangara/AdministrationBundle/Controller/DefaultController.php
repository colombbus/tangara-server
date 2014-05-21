<?php

namespace Tangara\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class DefaultController extends Controller {

    public function indexAction() {
        
        $fs = new Filesystem();

        try {
            $fs->touch('file.txt');
        } catch (IOException $e) {
            echo "An error occured while creating your directory";
        }
        
        return $this->render('TangaraAdministrationBundle:Default:index.html.twig');
    }

    public function dataAction() {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setData(array(
                'locale' => "fr",
                'projectId' => 121,
                'projectURL' => "http:\\apps.colombbus.org\tangara\files\121",
                'screen' => array('width' => 1024,
                    'height' => 768),
                'nbFiles' => 5,
                'files' => array('file0' => "pomme.gif",
                    'file1' => "arbre",
                    'file2' => "maison",
                    'file3' => "sol",
                    'file4' => "pomme.tgr"),
            ));
            return $response;
        }
    }

    public function getFileAction() {


    }

}
