<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends Controller {

    protected function renderMainTemplate($contentTemplate, $active, $contentPath) {
        $tangarajs = $this->get('router')->generate('tangara_core_homepage').$this->container->getParameter('tangara_core.settings.directory.tangarajs');
        return $this->render('TangaraCoreBundle::layout.html.twig', array(
                    'contentTemplate' => $contentTemplate,
                    'active' => $active,
                    'contentPath' => $contentPath,
                    'tangarajs' => $tangarajs));
    }
    
    public function indexAction() {
        return $this->renderMainTemplate('TangaraCoreBundle:Main:discover.html.twig', 'discover', 'tangara_core_discover');
    }
    
    public function discoverAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render('TangaraCoreBundle:Main:discover.html.twig');
        } else {
            // direct access
            return $this->renderMainTemplate('TangaraCoreBundle:Main:discover.html.twig', 'discover', 'tangara_core_discover');
        }
    }
    
    public function shareAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render('TangaraCoreBundle:Main:discover.html.twig');
        } else {
            // direct access
            return $this->renderMainTemplate('TangaraCoreBundle:Main:discover.html.twig', 'share', 'tangara_core_share');
        }
    }

    public function createAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            // should never occur
            $jsonResponse = new JsonResponse();
            return $jsonResponse->setData(array('error' => 'no-access'));
        } else {
            // direct access
            return $this->renderMainTemplate(false, 'create', false);
        }
    }
    
    
}
