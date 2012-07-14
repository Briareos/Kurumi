<?php

namespace Kurumi\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\City;

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
            'data_class' => 'Kurumi\UserBundle\Entity\User',
            'context' => 'user_picture',
        );
    }
}