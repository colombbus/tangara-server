<?php

namespace Tangara\CoreBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Manager\ProjectManager;

class UserListener implements EventSubscriberInterface {
    
    protected $manager;
    protected $session;
    protected $router;
    protected $authorizationChecker;
    protected $projectManager;
    
    function __construct(EntityManager $em, ProjectManager $pm,Session $session, Router $router, AuthorizationChecker $authorizationChecker) {
        $this->manager = $em;
        $this->projectManager = $pm;
        $this->session = $session;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }
    
    public static function getSubscribedEvents() {
        return array(
        FOSUserEvents::REGISTRATION_COMPLETED => 'onUserCreation',
        AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onUserLogin',
        FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onChangePasswordSuccess',
        FOSUserEvents::RESETTING_RESET_SUCCESS => 'onResettingPasswordSuccess',
        FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onUserImplicitLogin'
        );
    }
    
    public function onUserCreation(FilterUserResponseEvent $event) {
        $user = $event->getUser();
        // Create home project
        $home = new Project();
        $this->projectManager->saveProject($home);
        $this->projectManager->setHome($home, $user);
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
    
    public function onResettingPasswordSuccess(FormEvent $event) {
        $url = $this->router->generate('tangara_user_password_reset_confirmed');
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

    public function onUserImplicitLogin(UserEvent $event) {
        $user = $event->getUser();
        // No anonymous user
        $roles = $user->getRoles();
        if (in_array( 'ROLE_USER' , $roles )) {
            $home = $user->getHome();
            // set project as current project in session
            if ($home) {
                $this->session->set('projectid', $home->getId());
            }
        }
    }
    
}
