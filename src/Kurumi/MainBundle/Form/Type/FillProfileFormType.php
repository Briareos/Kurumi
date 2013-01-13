<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class FillProfileFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'create_user_profile';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birthday', 'birthday');
        $builder->add('gender', 'gender');
        $builder->add('lookingFor', 'gender');
        $builder->add('city', 'city');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => 'KurumiMainBundle:Profile',
                'validation_groups' => array('fill_profile'),
            )
        );
    }
}