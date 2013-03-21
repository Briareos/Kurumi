<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $builder->add('gender', 'gender');
        $builder->add('city', 'city');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kurumi\MainBundle\Entity\Profile',
            'validation_groups' => ['registration'],
        ]);
    }


}
