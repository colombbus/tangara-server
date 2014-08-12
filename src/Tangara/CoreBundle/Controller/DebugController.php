<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DebugController extends Controller {
    public function ajaxAction(){
        return $this->render('TangaraCoreBundle:Debug:ajax_debug.html.twig');
    }

}
