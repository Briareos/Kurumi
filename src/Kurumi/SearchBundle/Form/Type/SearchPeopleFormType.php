<?php

namespace Kurumi\SearchBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class SearchPeopleFormType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'search_people';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gender', 'gender');
        $builder->add('city', 'city');
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type' => 'get',
        ));
    }
}