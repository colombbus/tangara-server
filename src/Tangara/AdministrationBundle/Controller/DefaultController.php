<?php

namespace Tangara\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller {

    public function indexAction() {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setData(array(
                'data' => 121,
                'data0' => 120,
                'data1' => 125,
                'data2' => 124,
                'data3' => 129,
            ));
            return $response;
        }
        return $this->render('TangaraAdministrationBundle:Default:index.html.twig');
    }
    public function dataAction() {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setData(array(
                'data' => 121,
                'data0' => 120,
                'data1' => 125,
                'data2' => 124,
                'data3' => 129,
            ));
            return $response;
        }
    }
}
