<?php

namespace Tangara\CoreBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\GroupController as BaseController;

class FOSGroupController extends BaseController
{
    /*
     * Add controllers here if needed
     * 
     * public function newAction(Request $request) {
     *    $reponse = parent::newAction($request);
     *    return $response;
     * }
     * 
     */
    
    /**
     * 
     * Show all group and redirect to TangaraCoreBundle:list controller
     * @return type
     */
    public function listAction() {
        $response = parent::registerAction();

        //puis redirection vers le controller de tangara-ui
        return $this->redirect($this->generateUrl('tangara_core_group_list'));
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function newAction(Request $request) {
        
        $reponse = parent::newAction($request);
        
        return $reponse;
    }

    /**
     * 
     * Show all group and redirect to TangaraCoreBundle:list controller
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $groupName
     * @return type
     */
    public function editAction(Request $request, $groupName) {
        
        $reponse = parent::editAction($request, $groupName);

        return $response;
    }

}
