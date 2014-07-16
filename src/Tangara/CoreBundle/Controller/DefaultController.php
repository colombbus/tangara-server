<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tangara\CoreBundle\Entity\Group;
use Tangara\CoreBundle\Entity\User;


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
               
        //touver le leader du group, donc user puis som adresse email
        //$leader_mail = $group->getGroupLeader()->getMail();
        //$contenu_du_message = 'Bonjour je suis un Compte Test et je souhaite rejoindre ton groupe.';
        
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
    
}
