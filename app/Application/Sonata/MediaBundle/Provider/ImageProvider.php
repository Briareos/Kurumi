<?php

namespace Application\Sonata\MediaBundle\Provider;

use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;
use Sonata\MediaBundle\Model\MediaInterface;

class ImageProvider extends BaseImageProvider
{
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if ($format == 'reference') {
            $path = $this->getReferenceImage($media);
            $path = $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
        } else {
            $path = '/' . $this->thumbnail->generatePublicUrl($this, $media, $format);
        }

        return $path;
    }

    protected function doTransform(MediaInterface $media)
    {
        parent::doTransform($media);

        if ($media->getBinaryContent()) {
            try {
                $image = $this->imagineAdapter->open($media->getBinaryContent()->getPathname());
                $size = $image->getSize();

                $media->setWidth($size->getWidth());
                $media->setHeight($size->getHeight());

                $media->setProviderStatus(MediaInterface::STATUS_OK);
            } catch (\Exception $e) {
                $media->setProviderStatus(MediaInterface::STATUS_ERROR);
            }
        }
    }
}

