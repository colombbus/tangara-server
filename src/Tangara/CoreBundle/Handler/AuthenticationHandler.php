<?php
namespace Tangara\CoreBundle\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Tangara\CoreBundle\Manager\ProjectManager;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    private $router;
    private $session;
    private $templating;
    private $projectManager;
 
    public function __construct( RouterInterface $router, Session $session, TwigEngine $templating, ProjectManager $manager) {
        $this->router  = $router;
        $this->session = $session;
        $this->templating = $templating;
        $this->projectManager = $manager;
    }
 
    public function onAuthenticationSuccess( Request $request, TokenInterface $token ) {
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {
            $jsonResponse = new JsonResponse();
            $project = null;
            $projectId = $this->session->get('projectid');
            if ($projectId) {
                $project = $this->projectManager->getRepository()->find($projectId);
            }
            $content = $this->templating->render('TangaraCoreBundle:User:menu.html.twig', array('project'=>$project));
            return $jsonResponse->setData(array('success' => true, 'content'=>$content));
        // if form login
        } else {
            if ( $this->session->get('_security.main.target_path') ) {
                $url = $this->session->get('_security.main.target_path');
            } else {
                $url = $this->router->generate( 'tangara_core_homepage' );
            }
            return new RedirectResponse( $url );
        }
    }
 
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ) {
        // if AJAX login
        $this->session->set(SecurityContextInterface::AUTHENTICATION_ERROR,$exception);
        if ( $request->isXmlHttpRequest() ) {
            $jsonResponse = new JsonResponse();
            $content = $this->templating->render('TangaraCoreBundle:User:menu.html.twig', array('error' => true));
            return $jsonResponse->setData(array( 'success' => false, 'content' => $content));
        // if form login
        } else {
            return new RedirectResponse( $this->router->generate( 'login_route' ) );
        }
    }
}