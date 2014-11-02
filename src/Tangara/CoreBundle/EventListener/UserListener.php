<?php

namespace Tangara\CoreBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Tangara\CoreBundle\Entity\Project;

class UserListener implements EventSubscriberInterface {
    
    protected $manager;
    protected $session;
    protected $router;
    
    function __construct(EntityManager $em, Session $session, Router $router) {
        $this->manager = $em;
        $this->session = $session;
        $this->router = $router;
    }
    
    public static function getSubscribedEvents() {
        return array(
        FOSUserEvents::REGISTRATION_COMPLETED => 'onUserCreation',
        AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onUserLogin',
        FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onChangePasswordSuccess'
        );
    }
    
    public function onUserCreation(FilterUserResponseEvent $event) {
        $user = $event->getUser();
        // Create home project
        $home = new Project();
        $home->setOwner($user);
        $user->setHome($home);
        $this->manager->persist($home);
        $this->manager->persist($user);
        $this->manager->flush();
        // set project as current project in session
        $this->session->set('projectid', $home->getId());
    }
    
    public function onRegistrationSuccess(FormEvent $event) {
        $url = $this->router->generate('tangara_user_registration_confirmed');
        $event->setResponse(new RedirectResponse($url));
    }

    public function onChangePasswordSuccess(FormEvent $event) {
        $url = $this->router->generate('tangara_user_profile_show');
        $event->setResponse(new RedirectResponse($url));
    }
    
    public function onUserLogin(AuthenticationEvent $event) {
        $user = $event->getAuthenticationToken()->getUser();
        $home = $user->getHome();
        // set project as current project in session
        if ($home)
            $this->session->set('projectid', $home->getId());
    }

}
