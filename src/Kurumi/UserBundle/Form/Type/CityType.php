<?php

namespace Kurumi\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Kurumi\UserBundle\Form\DataTransformer\CityToCityNameTransformer;

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
        $builder->addViewTransformer(new CityToCityNameTransformer());
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'Kurumi\UserBundle\Entity\City',
            'validation_groups' => array('registration'),
        );
    }


}