<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfilePhotoFormType extends AbstractType
{
    public function getName()
    {
        return 'profile_photo';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\MediaBundle\Entity\Media',
            'provider' => 'sonata.media.provider.image',
            'context' => 'profile_photo',
            'validation_groups' => array('upload_photo'),
        ));
    }

    public function getParent()
    {
        return 'sonata_media_type';
    }
}