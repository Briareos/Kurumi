<?php

namespace Kurumi\MainBundle\Javascript;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Briareos\AjaxBundle\Javascript\JavascriptSettingsInjectorInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Briareos\NodejsBundle\Nodejs\Authenticator;
use Briareos\NodejsBundle\Entity\NodejsSubjectInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettingsContainer;

class NodejsAuthenticationInjector implements JavascriptSettingsInjectorInterface
{
    private $nodejsAuthenticator;

    private $securityContext;

    public function __construct(Authenticator $nodejsAuthenticator, SecurityContextInterface $securityContext)
    {
        $this->nodejsAuthenticator = $nodejsAuthenticator;
        $this->securityContext = $securityContext;
    }

    public function onRequest(GetResponseEvent $event, JavascriptSettingsContainer $settingsContainer)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }
        $nodejsAuthToken = $this->getNodejsAuthToken($event->getRequest());
        $settingsContainer->addJavascriptSettings(new JavascriptSettings('nodejs_authenticate', [
            'nodejs_auth_token' => $nodejsAuthToken,
        ]));
    }

    public function getNodejsAuthToken(Request $request, $authenticate = true)
    {
        if ($this->securityContext->getToken() === null) {
            return false;
        }
        $user = $this->securityContext->getToken()->getUser();
        if (!$user instanceof NodejsSubjectInterface) {
            return false;
        }
        $session = $request->getSession();
        if (!$session instanceof SessionInterface) {
            return false;
        }
        if ($authenticate) {
            $this->nodejsAuthenticator->authenticate($session, $user);
        }
        $nodejsAuthToken = $this->nodejsAuthenticator->generateAuthToken($session, $user);
        return $nodejsAuthToken;
    }
}
