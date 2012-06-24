<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\City;

class UserPictureFormType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'user_picture';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('picture', 'sonata_media_type', array(
            'data' => $options['data']->getPicture(),
            'provider' => 'sonata.media.provider.image',
        ));
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'App\UserBundle\Entity\User',
            'context' => 'user_picture',
        );
    }
}