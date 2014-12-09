<?php

namespace Tangara\CoreBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Tangara\CoreBundle\Entity\Project;

class UserListener implements EventSubscriberInterface {
    
    protected $manager;
    protected $session;
    protected $router;
    protected $authorizationChecker;
    
    function __construct(EntityManager $em, Session $session, Router $router, AuthorizationChecker $authorizationChecker) {
        $this->manager = $em;
        $this->session = $session;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
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
        // No anonymous user
        $roles = $event->getAuthenticationToken()->getRoles();
        $anonymous = true;
        foreach ($roles as $role) {
            if ($role->getRole() === "ROLE_USER") {
                $anonymous = false;
                break;
            }
        }
        if (!$anonymous) {
            $user = $event->getAuthenticationToken()->getUser();
            $home = $user->getHome();
            // set project as current project in session
            if ($home) {
                $this->session->set('projectid', $home->getId());
            }
        }
    }

}
