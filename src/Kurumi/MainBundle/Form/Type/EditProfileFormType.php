<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditProfileFormType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'edit_profile';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name');
        $builder->add('last_name');
        $builder->add('gender','gender');
        $builder->add('birthday','birthday');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'class' => 'KurumiMainBundle:Profile',
        ]);
    }


}
