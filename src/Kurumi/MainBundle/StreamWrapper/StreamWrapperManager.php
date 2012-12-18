<?php

namespace Kurumi\MainBundle\StreamWrapper;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;
use Kurumi\MainBundle\StreamWrapper\AbstractLocalStreamWrapper;

class StreamWrapperManager
{
    /**
     * @var array
     */
    private $streamWrappers;

    /**
     * @var RouterInterface
     */
    private $router;


    function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function addStreamWrapper(AbstractLocalStreamWrapper $streamWrapper, $scheme, $path, $route)
    {
        $this->streamWrappers[$scheme] = [
            'class' => get_class($streamWrapper),
            'path' => $path,
            'scheme' => $scheme,
            'instance' => $streamWrapper,
            'route' => $route,
        ];
    }

    public function getStreamWrapper($scheme)
    {
        if (!isset($this->streamWrappers[$scheme])) {
            throw new \RuntimeException(sprintf('Stream wrapper "%s" is not registered.'));
        }

        return $this->streamWrappers[$scheme];
    }

    public function getExternalUrl($uri)
    {
        $wrapperInfo = $this->getWrapperFor($uri);
        $target = $this->getTarget($uri);
        $url = $this->router->generate($wrapperInfo['route'], ['path' => $target]);

        return $url;
    }

    /**
     * @param $uri
     * @return AbstractLocalStreamWrapper
     */
    public function getWrapperInstanceFor($uri)
    {
        return $this->getWrapperFor($uri)['instance'];
    }

    public function getWrapperFor($uri)
    {
        $scheme = $this->getScheme($uri);
        $wrapperInfo = $this->streamWrappers[$scheme];

        return $wrapperInfo;
    }

    public function realpath($uri)
    {
        $wrapper = $this->getWrapperInstanceFor($uri);
        $wrapper->setUri($uri);
        $realpath = $wrapper->realpath();

        return $realpath;
    }

    public function getScheme($uri)
    {
        list($scheme, $target) = explode('://', $uri, 2);

        return $scheme;
    }

    public function getTarget($uri)
    {
        list($scheme, $target) = explode('://', $uri, 2);

        return $target;
    }

    public function registerStreamWrappers()
    {
        foreach ($this->streamWrappers as $scheme => $info) {
            $class = $info['class'];
            /** @var $class AbstractLocalStreamWrapper */
            $class::setDirectoryPath($info['path']);
            $registeredWrappers = stream_get_wrappers();
            if (!in_array($scheme, $registeredWrappers)) {
                stream_wrapper_register($scheme, $class);
            }
        }
    }
}