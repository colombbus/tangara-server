<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Project;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('TangaraCoreBundle:Default:homepage.html.twig');
    }

    public function localeAction() {
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if ($request) {
            $response = new JsonResponse();
            $response->setData(array(
                'locale' => $locale));
            return $response;
        }
    }

    public function ajaxAction() {
        return $this->render('TangaraCoreBundle:Default:ajax_symfony.html.twig');
    }

    public function sessionAction(Project $project) {

        $request = $this->getRequest();
        $session = $request->getSession();
        $session->set('projectid', $project->getId());
        return $this->forward('TangaraCoreBundle:Project:create');
    }

    /**
     * Create a program with TangaraJS
     * 
     */
    public function createAction() {
        $tangarajs = $this->container->getParameter('tangara_core.settings.directory.tangarajs');
        //if ($tangarajs == null) {}
        $projectId = $this->get('request')->get('projectid');

        return $this->render('TangaraCoreBundle:Project:create.html.twig', array(
                    'tangarajs' => $tangarajs,
                    'projectid' => $projectId
        ));
    }

}
