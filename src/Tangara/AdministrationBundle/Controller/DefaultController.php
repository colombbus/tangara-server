<?php

namespace Tangara\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TangaraAdministrationBundle:Default:index.html.twig');
    }
	public function studentAction()
    {
        return $this->render('TangaraAdministrationBundle:Default:student.html.twig');
    }
	public function studentchiefAction()
    {
        return $this->render('TangaraAdministrationBundle:Default:studentchief.html.twig');
    }
	public function teacherAction()
    {
        return $this->render('TangaraAdministrationBundle:Default:teacher.html.twig');
    }
	public function adminAction()
    {
        return $this->render('TangaraAdministrationBundle:Default:admin.html.twig');
    }

}
