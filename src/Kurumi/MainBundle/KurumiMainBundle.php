<?php

namespace Kurumi\MainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kurumi\MainBundle\DependencyInjection\Compiler\RemoveAjaxWebDebugToolbarPass;

class KurumiMainBundle extends Bundle
{
    public function boot()
    {
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RemoveAjaxWebDebugToolbarPass());
    }

}
