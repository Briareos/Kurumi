<?php

namespace Kurumi\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Kurumi\UserBundle\Form\Type\CityType;

class RegisterProfileType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'register_profile';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birthday', 'birthday');
        $builder->add('gender', 'gender', array(
        ));
        $builder->add('city', 'city', array(
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kurumi\UserBundle\Entity\Profile',
            'validation_groups' => array('registration'),
        ));
    }


}