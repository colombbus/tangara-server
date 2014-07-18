<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;



class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('TangaraCoreBundle:Default:homepage.html.twig');
    }
    
    //controleur vers la page de confirmation
    public function confirmationAction() {
        
        //recuperer le formulaire
        $msg = $this->container->get('request')->get('object');
        $goupId = $this->container->get('request')->get('groups');
                      
        $group = $this->getDoctrine()->getManager()
                ->getRepository('TangaraCoreBundle:Group')
                ->find($goupId);
               
        //touver le leader du group, donc user puis som adresse email
        $user = $group->getGroupsLeader();     
        $leader_mail = $user->getEmail();
       
        
        //envoyer un mail au leader
        $message = \Swift_Message::newInstance()
        ->setSubject('Demande de rejoidre le groupe')
        ->setFrom('tangaraui@colombbus.org')
        ->setTo('tangaraui@colombbus.org') //a changer avec le mail $leader_mail 
        ->setBody($msg)
        ;
        $this->container->get('mailer')->send($message);
        
        
        
       
        //return $this->render('TangaraCoreBundle:Project:confirmation.html.twig');
        return new Response('Message envoyé');
    }
    
    
    
    public function ajaxAction(){
        return $this->render('TangaraCoreBundle:Default:ajax_symfony.html.twig');;
    }
    
}
