<?php

namespace Tangara\CoreBundle\Controller;

use Tangara\CoreBundle\Controller\TangaraController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends TangaraController {

    public function indexAction() {
        return $this->discoverAction();
    }
    
    public function discoverAction() {
        $discoverUrl = $this->container->getParameter('tangara_core.settings.url.discover');
        if (!$discoverUrl) {
            return $this->renderContent('TangaraCoreBundle:Main:discover.html.twig', 'discover');
        } else {
            return $this->renderContent('TangaraCoreBundle:Main:discover_remote.html.twig', 'discover', array('url'=>$discoverUrl));
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
