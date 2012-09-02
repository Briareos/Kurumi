<?php

namespace Kurumi\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\MediaBundle\Entity\Media',
            'provider' => 'sonata.media.provider.image',
            'context' => 'user_picture',
            'validation_groups' => array('edit_picture'),
        ));
    }

    public function getParent()
    {
        return 'sonata_media_type';
    }


}