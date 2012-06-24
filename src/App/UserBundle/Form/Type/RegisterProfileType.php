<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\UserBundle\Form\Type\CityType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

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
        $builder->add('birthday', 'birthday', array(
            'empty_value' => array(
                'year' => 'Year',
                'month' => 'Month',
                'day' => 'Day'
            ),
            'years' => range(date('Y'), date('Y') - 100),
        ));
        $builder->add('gender', 'gender', array(
            'expanded' => true,
            //'index_strategy' => ChoiceList::COPY_CHOICE,
            //'value_strategy' => ChoiceList::COPY_CHOICE,
        ));
        $builder->add('city', new CityType(), array(
            'data' => $options['data']->getCity(),
            'error_bubbling' => false,
        ));
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'App\UserBundle\Entity\Profile',
            'validation_groups' => array('registration'),
        );
    }


}