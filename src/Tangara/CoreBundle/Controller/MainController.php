<?php

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends TangaraController {

    public function indexAction() {
        return $this->createAction();
    }
    
    public function infoAction() {
        return $this->renderContent('TangaraCoreBundle:Main:info.html.twig', 'info');
    }

    public function createAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            // should never occur
            $jsonResponse = new JsonResponse();
            return $jsonResponse->setData(array('error' => 'no-access'));
        } else {
            // direct access
            return $this->renderContent(false, 'create');
        }
    }
    
    
}
