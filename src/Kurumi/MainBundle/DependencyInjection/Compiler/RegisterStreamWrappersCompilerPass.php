<?php

namespace Kurumi\MainBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterStreamWrappersCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('stream_wrapper.manager')) {
            return;
        }
        $streamWrapperManagerDefinition = $container->getDefinition('stream_wrapper.manager');
        $streamWrapperServices = $container->findTaggedServiceIds('stream_wrapper');
        foreach ($streamWrapperServices as $id => $tags) {
            foreach ($tags as $tag) {
                $streamWrapperManagerDefinition->addMethodCall('addStreamWrapper', array(new Reference($id), $tag['scheme'], $tag['path'], $tag['route']));
            }
        }
    }

}