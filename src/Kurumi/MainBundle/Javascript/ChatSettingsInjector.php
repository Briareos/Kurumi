<?php

namespace Kurumi\MainBundle\Javascript;

use Briareos\AjaxBundle\Javascript\JavascriptSettingsInjectorInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettings;
use Symfony\Component\Routing\RouterInterface;
use Briareos\AjaxBundle\Javascript\JavascriptSettingsContainer;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class ChatSettingsInjector implements JavascriptSettingsInjectorInterface
{
    private $defaultContainer;

    private $templates;

    private $routes;

    private $templating;

    private $router;

    function __construct($defaultContainer, array $templates, array $routes, EngineInterface $templating, RouterInterface $router)
    {
        $this->defaultContainer = $defaultContainer;
        $this->templates = $templates;
        $this->routes = $routes;
        $this->templating = $templating;
        $this->router = $router;
    }

    public function onRequest(GetResponseEvent $event, JavascriptSettingsContainer $settingsContainer)
    {
        if (!$event->getRequest()->isXmlHttpRequest()) {
            $settings = new JavascriptSettings('default_chat', $this->getSettings());
            $settingsContainer->addJavascriptSettings($settings);
        }
    }

    public function getSettings()
    {
        $compiledTemplates = [];
        foreach ($this->templates as $templateKey => $templateName) {
            $compiledTemplates[$templateKey] = $this->templating->render($templateName);
        }

        $compiledRoutes = [];
        foreach ($this->routes as $routeKey => $routeName) {
            $compiledRoutes[$routeKey] = $this->router->generate($routeName);
        }

        return [
            'container' => $this->defaultContainer,
            'tpl' => $compiledTemplates,
            'url' => $compiledRoutes,
        ];
    }
}
