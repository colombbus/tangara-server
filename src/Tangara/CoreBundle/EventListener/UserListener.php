<?php

namespace Tangara\CoreBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Tangara\CoreBundle\Entity\Project;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class UserListener implements EventSubscriberInterface {
    
    protected $manager;
    protected $session;
    
    function __construct(EntityManager $em, Session $session) {
        $this->manager = $em;
        $this->session = $session;
    }
    
    public static function getSubscribedEvents() {
        return array(
        FOSUserEvents::REGISTRATION_COMPLETED => 'onUserCreation',
        AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onUserLogin'
        );
    }
    
    public function onUserCreation(FilterUserResponseEvent $event) {
        $user = $event->getUser();
        // Create home project
        $home = new Project();
        $home->setOwner($user);
        $this->manager->persist($home);
        $this->manager->flush();
        // set project as current project in session
        $this->session->set('projectid', $home->getId());
    }
    
    public function onUserLogin(AuthenticationEvent $event) {
        $user = $event->getAuthenticationToken()->getUser();
        $home = $user->getHome();
        // set project as current project in session
        if ($home)
            $this->session->set('projectid', $home->getId());
    }

}
