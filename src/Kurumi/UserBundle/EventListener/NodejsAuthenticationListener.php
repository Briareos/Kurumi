<?php

namespace Kurumi\UserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Briareos\NodejsBundle\Nodejs\Authenticator;
use Briareos\NodejsBundle\Entity\NodejsSubjectInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettingsContainer;

class NodejsAuthenticationListener
{
    private $nodejsAuthenticator;

    private $securityContext;

    private $javascriptSettingsContainer;

    public function __construct(Authenticator $nodejsAuthenticator, SecurityContextInterface $securityContext, JavascriptSettingsContainer $javascriptSettingsContainer)
    {
        $this->nodejsAuthenticator = $nodejsAuthenticator;
        $this->securityContext = $securityContext;
        $this->javascriptSettingsContainer = $javascriptSettingsContainer;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }
        $nodejsAuthToken = $this->getNodejsAuthToken($event->getRequest());
        $this->javascriptSettingsContainer->addJavascriptSettings(new JavascriptSettings('nodejs_authenticate', array(
            'nodejs_auth_token' => $nodejsAuthToken,
        )));
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
