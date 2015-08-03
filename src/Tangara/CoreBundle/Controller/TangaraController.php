<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class TangaraController extends Controller {

    protected function isUserLogged() {
        $context = $this->container->get('security.context');
        return $context->isGranted('IS_AUTHENTICATED_FULLY');
    }
    
    protected function getProject() {
        $session = $this->getRequest()->getSession();
        $projectId = $session->get('projectid');
        if (!$projectId) {
            return null;
        }
        // Check if project exists
        $project = $this->get('tangara_core.project_manager')->getRepository()->find($projectId);
        if (!$project) {
            return null;
        }
        
        return $project;
    }
    
    protected function renderContent($contentTemplate, $active, $parameters = array(), $learn = false) {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('POST')) {
                $jsonResponse = new JsonResponse();
                return $jsonResponse->setData(array('content'=>$this->renderView($contentTemplate, $parameters)));
            } else {
                return $this->render($contentTemplate, $parameters);
            }
        } else {
            $baseUrl = $this->getRequest()->getSchemeAndHttpHost()."/";
            $tangarajslearn = $baseUrl.$this->container->getParameter('tangara_core.settings.directory.tangarajs')."/learn.html";
            $tangarajs = $baseUrl.$this->container->getParameter('tangara_core.settings.directory.tangarajs')."/index.html";
            $tangaratutorial = $baseUrl.$this->container->getParameter('tangara_core.settings.directory.tutorial');

            if ($contentTemplate) {
                $contentUrl =  $request->getUri();
            } else {
                $contentUrl = false;
            }
            $parameters = array_merge($parameters, array('contentTemplate' => $contentTemplate,
                        'active' => $active,
                        'contentUrl' => $contentUrl,
                        'tangarajs' => $tangarajs,
                        'tangarajslearn' => $tangarajslearn,
                        'tangaratutorial' => $tangaratutorial,
                        'learn' => $learn,
                        'current_project' => $this->getProject()));
            return $this->render("TangaraCoreBundle::layout.html.twig", $parameters);
        }
    }
    
    public function redirect($url, $status = 302) {
        $request = $this->getRequest();
        if ($request->isMethod('POST') && $request->isXmlHttpRequest()) {
            $jsonResponse = new JsonResponse();
            return $jsonResponse->setData(array('redirect' => $url));
        } else {
            return parent::redirect($url, $status);
        }
    }
    
}
