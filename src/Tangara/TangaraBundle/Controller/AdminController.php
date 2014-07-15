<?php

namespace Tangara\TangaraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Tangara\TangaraBundle\Form\ProjectType;
use Tangara\TangaraBundle\Entity\Document;
use Tangara\TangaraBundle\Entity\Project;
use Tangara\UserBundle\Entity\User;
use Tangara\UserBundle\Entity\Group;

class AdminController extends Controller {

    public function indexAction() {
        return $this->render('TangaraTangaraBundle:Admin:homepage.html.twig');
    }
}
