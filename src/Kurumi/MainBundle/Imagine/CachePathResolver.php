<?php

namespace Kurumi\MainBundle\Imagine;

use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver as BaseCachePathResolver;

class CachePathResolver extends BaseCachePathResolver
{
    public function getBrowserPath($path, $filter, $absolute = false)
    {
        return parent::getBrowserPath($path, $filter, $absolute);
    }

}