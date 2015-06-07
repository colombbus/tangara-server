<?php

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends TangaraController {

    public function indexAction() {
        return $this->renderContent('TangaraCoreBundle:Main:discover.html.twig', 'discover');
    }
    
    public function discoverAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            // should never occur
            $jsonResponse = new JsonResponse();
            return $jsonResponse->setData(array('error' => 'no-access'));
        } else {
            // direct access
            return $this->renderContent(false, 'discover', array());
        }
    }
    
    public function shareAction() {
        return $this->renderContent('TangaraCoreBundle:Main:discover.html.twig', 'share');
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
