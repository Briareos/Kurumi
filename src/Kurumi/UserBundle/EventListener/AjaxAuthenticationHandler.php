<?php

namespace Kurumi\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AjaxAuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

    /**
     * @var Router
     */
    private $router;

    private $messages = array(
        'Bad credentials' => 'USER_NOT_FOUND',
        'The presented password cannot be empty.' => 'PASSWORD_EMPTY',
        'The presented password is invalid.' => 'PASSWORD_INVALID',
        'Invalid CSRF token.' => 'CSRF_INVALID',
        'USERNAME_EMPTY',
    );

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response the response to return
     */
    function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $result = array();
            $result['modal'] = array(
                'body' => (string)$exception,
            );
            $response = new Response(json_encode($result), 200, array(
                'Content-Type' => 'application/json',
            ));
        } else {
            $response = new RedirectResponse($this->router->generate('front'));
        }
        return $response;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response the response to return
     */
    function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($request->isXmlHttpRequest()) {
            $result = array();
            $result['location'] = array(
                'url' => $this->router->generate('front'),
            );
            $response = new Response(json_encode($result), 200, array(
                'Content-Type' => 'application/json',
            ));
        } else {
            $response = new RedirectResponse($this->router->generate('front'));
        }
        return $response;
    }


}