<?php

namespace Kurumi\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\City;

class RegisterFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_register';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
        ));
        $builder->add('plainPassword', 'password', array(
        ));
        $builder->add('profile', new RegisterProfileType(), array(
            'data' => $options['data']->getProfile(),
        ));
        $builder->get('profile')->setErrorBubbling(true);
        $builder->add('name', 'text', array(
        ));
        $builder->add('timezone', 'detect_timezone', array(
        ));
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'Kurumi\UserBundle\Entity\User',
            'validation_groups' => array('registration'),
        );
    }


}