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

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    private $router;
    private $session;
    private $templating;
 
    public function __construct( RouterInterface $router, Session $session, TwigEngine $templating) {
        $this->router  = $router;
        $this->session = $session;
        $this->templating = $templating;
    }
 
    public function onAuthenticationSuccess( Request $request, TokenInterface $token ) {
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {
            $jsonResponse = new JsonResponse();
            $content = $this->templating->renderResponse('TangaraCoreBundle:Main:usermenu.html.twig');            
            return $jsonResponse->setData(array('success' => true, 'content'=>$content));
        // if form login
        } else {
            if ( $this->session->get('_security.main.target_path') ) {
                $url = $this->session->get('_security.main.target_path');
            } else {
                $url = $this->router->generate( 'home_page' );
            } // end if
            return new RedirectResponse( $url );
        }
    }
 
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ) {
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {
            $jsonResponse = new JsonResponse();
            $content = $this->templating->render('TangaraCoreBundle:Main:usermenu.html.twig', array(
                'error' => $exception->getMessage()));
            return $jsonResponse->setData(array( 'success' => false, 'content' => $content));
        // if form login
        } else {
            // set authentication exception to session
            $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);
            return new RedirectResponse( $this->router->generate( 'login_route' ) );
        }
    }
}