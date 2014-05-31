<?php

namespace Tangara\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('TangaraAdministrationBundle:Default:homepage.html.twig');
    }
    
    public function createAction() {
        return $this->render('TangaraAdministrationBundle:Default:create.html.twig');
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

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setData(array(
                'files' => array("niveau1.tgr","niveau2.tgr", "promeneur.tgr", "fin.tgr")
            ));
            return $response;
        }
    }

    public function localeAction() {
        $request = $this->getRequest();
        $locale = $this->getRequest()->getLocale();

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setData(array(
                'locale' => $locale));
            return $response;
        }
    }
}
