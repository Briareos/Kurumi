<?php

namespace App\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

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
            $result = array('success' => false);
            if ($request->request->get('_username', false)) {
                if (isset($this->messages[$exception->getMessage()])) {
                    $result['message'] = $this->messages[$exception->getMessage()];
                } else {
                    $result['message'] = $exception->getMessage();
                }
            } else {
                $result['message'] = 'USERNAME_EMPTY';
            }
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
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
            $result = array('success' => true);
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = new RedirectResponse($this->router->generate('front'));
        }
        return $response;
    }


}