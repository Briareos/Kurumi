<?php

namespace Kurumi\UserBundle\User;

use Doctrine\ORM\EntityManager;
use Sonata\MediaBundle\Provider\Pool;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Kurumi\UserBundle\Entity\User;
use Briareos\ChatBundle\Subject\PictureProviderInterface;

class PictureProvider implements PictureProviderInterface
{
    private $pictures = array();

    private $mediaService;

    private $subjectPictureFormat;

    public function __construct(EntityManager $em, $className, array $galleries, Pool $mediaService, $subjectPictureFormat)
    {
        $this->mediaService = $mediaService;
        $this->subjectPictureFormat = $subjectPictureFormat;

        $repository = $em->getRepository($className);

        $qb = $em->createQueryBuilder();
        $qb->from($repository->getClassName(), 'g', 'g.id');
        $qb->select('g');
        $qb->addSelect('ghm');
        $qb->addSelect('m');
        $qb->innerJoin('g.galleryHasMedias', 'ghm');
        $qb->innerJoin('ghm.media', 'm');
        $qb->where($qb->expr()->in('g.id', $galleries));
        $loadedGalleries = $qb->getQuery()->execute();

        foreach (array(0 => 'unknown', 1 => 'male', 2 => 'female') as $genderId => $gender) {
            /** @var $gallery \Sonata\MediaBundle\Model\GalleryInterface */
            $gallery = $loadedGalleries[$galleries[$gender]];
            /** @var $galleryHasMedia \Sonata\MediaBundle\Model\GalleryHasMediaInterface */
            foreach ($gallery->getGalleryHasMedias() as $galleryHasMedia) {
                $this->addPicture($genderId, $galleryHasMedia->getMedia());
            }
        }
    }

    public function getSubjectPicture(ChatSubjectInterface $subject)
    {
        /** @var $subject User */
        $media = $this->getPicture($subject);

        $provider = $this->mediaService->getProvider($media->getProviderName());

        $format = $provider->getFormatName($media, $this->subjectPictureFormat);

        return $provider->generatePublicUrl($media, $format);
        /*
         * @see \Sonata\MediaBundle\Twig\Extension\MediaExtension
         *
        $options['src'] = $provider->generatePublicUrl($media, $format);

        return $this->render($provider->getTemplate('helper_thumbnail'), array(
            'media'    => $media,
            'options'  => $options,
        ));
        */
    }

    public function getPicture(User $user)
    {
        if ($user->getPicture() !== null) {
            return $user->getPicture();
        }
        $gender = $user->getProfile()->getGender() ? $user->getProfile()->getGender() : 0;
        $availablePictures = count($this->pictures[$gender]);
        $pictureIndex = $user->getId() & $availablePictures - 1;
        return $this->pictures[$gender][$pictureIndex];
    }

    public function addPicture($genderId, MediaInterface $picture)
    {
        $this->pictures[$genderId][] = $picture;
    }
}