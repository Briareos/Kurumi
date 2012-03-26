<?php

namespace App\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RegisterFormType extends AbstractType {

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_register';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'email')
            ->add('password', 'repeated', array(
                'type' => 'password'
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'App\UserBundle\Entity\User',
        );
    }


}