<?php

namespace Kurumi\MainBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveAjaxWebDebugToolbarPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('web_profiler.debug_toolbar')) {
            $container->removeDefinition('web_profiler.ajax_debug_toolbar');
        }
    }


}
