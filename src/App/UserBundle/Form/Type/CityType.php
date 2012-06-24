<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CityType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'city';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullName', 'text', array(
            'error_bubbling' => true,
        ));
        $builder->add('geonameId', 'hidden', array(
            'error_bubbling' => true,
        ));
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'App\UserBundle\Entity\City',
            'attr' => array(
                'data-country-code' => '',
                'data-country-name' => '',
            ),
            'validation_groups' => array('registration'),
        );
    }


}