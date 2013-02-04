<?php

namespace Kurumi\MainBundle\InfoProvider;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Doctrine\ORM\Proxy\Proxy;
use Kurumi\MainBundle\Entity\Picture;
use Vich\UploaderBundle\Storage\StorageInterface;

class PictureInfoProvider
{
    private $storage;

    private $cacheManager;

    public function __construct(StorageInterface $storage, CacheManager $cacheManager)
    {
        $this->storage = $storage;
        $this->cacheManager = $cacheManager;
    }

    private function load(Picture $picture)
    {
        if ($picture instanceof Proxy && !$picture->__isInitialized()) {
            $picture->__load();
        }
    }

    public function getPicturePath(Picture $picture, $format = null)
    {
        $webRoot = $this->cacheManager->getWebRoot();
        $picturePath = $this->getPictureUrl($picture, $format);

        // @HACK
        // Since we can't access the request scope to get the base path, we must
        // strip /app_dev.php/ and such from the start of the picture path.
        if (preg_match('{^/[a-z0-9_-]+\.php/}', $picturePath, $matches)) {
            $picturePath = '/' . substr($picturePath, strlen($matches[0]));
        }
        $path = $webRoot . $picturePath;

        return $path;
    }

    public function getPictureUri(Picture $picture)
    {
        $this->load($picture);
        $uri = $this->storage->resolvePath($picture, 'file');

        return $uri;
    }

    public function getPictureUrl(Picture $picture, $format = null)
    {
        $this->load($picture);
        $uri = $this->storage->resolveUri($picture, 'file');
        if ($format !== null) {
            $uri = $this->cacheManager->getBrowserPath($uri, $format);
        }

        return $uri;
    }

    public function getPictureInfo(Picture $picture, $format = null)
    {
        $path = $this->getPicturePath($picture, $format);
        $imageInfo = null;
        if (file_exists($path)) {
            $imageInfo = getimagesize($path);
        }

        return $imageInfo;
    }
}