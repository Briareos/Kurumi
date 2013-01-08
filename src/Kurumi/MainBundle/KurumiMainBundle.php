<?php

namespace Kurumi\MainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Kurumi\MainBundle\DependencyInjection\Compiler\RegisterStreamWrappersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KurumiMainBundle extends Bundle
{
    public function boot()
    {
        if ($this->container->has('stream_wrapper.manager')) {
            /** @var $streamManager \Kurumi\MainBundle\StreamWrapper\StreamWrapperManager */
            $streamManager = $this->container->get('stream_wrapper.manager');
            //$streamManager->registerStreamWrappers();
        }
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterStreamWrappersCompilerPass());
    }

}
