<?php

namespace Kurumi\MainBundle\Javascript;

use Briareos\AjaxBundle\Javascript\JavascriptSettingsInjectorInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettings;
use Briareos\NodejsBundle\Nodejs\DispatcherInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettingsContainer;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class NodejsSettingsInjector implements JavascriptSettingsInjectorInterface
{
    private $dispatcher;

    function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function onRequest(GetResponseEvent $event, JavascriptSettingsContainer $settingsContainer)
    {
        if (!$event->getRequest()->isXmlHttpRequest()) {
            $settings = new JavascriptSettings('default_nodejs', $this->getSettings());
            $settingsContainer->addJavascriptSettings($settings);
        }
    }

    public function getSettings()
    {
        return [
            'secure' => $this->dispatcher->getSecure(),
            'host' => $this->dispatcher->getHost(),
            'port' => $this->dispatcher->getPort(),
            'resource' => $this->dispatcher->getResource(),
            'connectTimeout' => $this->dispatcher->getConnectTimeout(),
            'contentTokens' => '',
        ];
    }
}
