<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity;

class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('TangaraCoreBundle:Default:homepage.html.twig');
    }
    
    //controleur vers la page de confirmation
    public function confirmationAction() {
        
        //recuperer le formulaire
        
        //echo $this->container->get('request')->get('object');
        $msg = $this->container->get('request')->get('object');
        //echo $this->container->get('request')->get('groups');
        $goupId = $this->container->get('request')->get('groups');
        
        
                
        $group = $this->getDoctrine()->getManager()
                ->getRepository('TangaraCoreBundle:Group')
                ->find($goupId);
        
        
        echo $group->getId();
        
        
        //touver le leader du group, donc user puis som adresse email
        $leader_mail = $group->getGroupLeader()->getMail();
        
        
        
        
        /*
        //envoyer un mail au leader
        $message = \Swift_Message::newInstance()
        ->setSubject('Demande de rejoidre le groupe')
        ->setFrom('test@example.com')
        ->setTo('group_leader@example.com')
        ->setBody($contenu_du_message)
        ;
        $this->container->get('mailer')->send($message);
        */
       
        //return $this->render('TangaraCoreBundle:Project:confirmation.html.twig');
        return new Response('Message envoyé');
    }
    
}
