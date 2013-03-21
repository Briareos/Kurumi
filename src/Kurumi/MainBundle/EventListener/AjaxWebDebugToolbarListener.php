<?php

namespace Kurumi\MainBundle\EventListener;

use Symfony\Bundle\WebProfilerBundle\EventListener\WebDebugToolbarListener;
use Symfony\Bundle\WebProfilerBundle\Profiler\TemplateManager;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\RouterInterface;
use Briareos\AjaxBundle\Ajax;

class AjaxWebDebugToolbarListener extends WebDebugToolbarListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Profiler
     */
    private $profiler;

    /**
     * @var TemplateManager
     */
    private $templateManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    private $dataCollectorTemplates;

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function setProfiler(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function setDataCollectorTemplates($dataCollectorTemplates)
    {
        $this->dataCollectorTemplates = $dataCollectorTemplates;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$request->isXmlHttpRequest()) {
            parent::onKernelResponse($event);

            return;
        }

        if (!$response instanceof Ajax\Response
            || !$response->headers->has('X-Debug-Token')
            || ($response->isRedirect() && !$this->interceptRedirects)
        ) {
            return;
        }

        $token = $response->headers->get('X-Debug-Token');

        $profile = $this->profiler->loadProfile($token);

        $url = null;
        try {
            $url = $this->router->generate('_profiler', ['token' => $token]);
        } catch (\Exception $e) {
            // the profiler is not enabled
        }

        $toolbarHtml = $toolbar = $this->templating->render(
            'WebProfilerBundle:Profiler:toolbar.html.twig',
            [
                'position' => 'normal', // This is to not include any CSS
                'profile' => $profile,
                'templates' => $this->getTemplateManager()->getTemplates($profile),
                'profiler_url' => $url,
            ]
        );

        /** @var $response Ajax\Response */
        $commandContainer = $response->getContent();

        $command = new Ajax\Command\Html('.sf-toolbarreset', $toolbarHtml, true);
        $commandContainer->add($command);
    }

    public function getTemplateManager()
    {
        if ($this->templateManager === null) {
            $this->templateManager = new TemplateManager(
                $this->profiler,
                $this->templating,
                $this->twig,
                $this->dataCollectorTemplates
            );
        }

        return $this->templateManager;
    }
}
