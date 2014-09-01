<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//use Symfony\Component\HttpFoundation\Response;

class DebugController extends Controller {

    public function ajaxAction() {
        return $this->render('TangaraCoreBundle:Debug:ajax_debug.html.twig');
    }

    public function setProjectIdAction($project_id) {
        $request = $this->getRequest();
        $session = $request->getSession();
        $projectId = $session->set('projectid', $project_id);
        $jsonResponse = new JsonResponse();
        $jsonError = new JsonResponse();
        $securityContext = $this->container->get('security.context');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $jsonError->setData(array('error' => 'user_not_logged'));
        }
        return $jsonResponse->setData(array('projectid' => $project_id));
    }

}
