<?php

namespace Kurumi\MainBundle\Imagine;

use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver as BaseCachePathResolver;
use Symfony\Component\Routing\RouterInterface;

class CachePathResolver extends BaseCachePathResolver
{
    private $webRoot;

    private $router;

    public function __construct($webRoot, RouterInterface $router)
    {
        $this->webRoot = $webRoot;
        $this->router = $router;
    }

    public function getBrowserPath($path, $filter, $absolute = false)
    {
        return parent::getBrowserPath($path, $filter, $absolute);
    }

}