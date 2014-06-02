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
