<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        $tangarajs = $this->container->getParameter('tangara_core.settings.directory.tangarajs');
        $urlDiscover = $this->get('router')->generate('tangara_core_discover');
        // TODO: replace url
        $urlShare = $this->get('router')->generate('tangara_core_discover');
        return $this->render('TangaraCoreBundle::layout.html.twig', array(
                    'tangarajs' => $tangarajs,
                    'urlDiscover' => $urlDiscover,
                    'urlShare' => $urlShare));
    }
    
    public function discoverAction() {
        return $this->render('TangaraCoreBundle:Main:discover.html.twig');
    }
    
}
