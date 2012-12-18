<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
        $builder->add('lookingFor', 'choice', array(
            'choices' => array(
                1 => 'Men',
                2 => 'Women',
                3 => 'Both',
            ),
        ));
        $builder->add('city', 'city', array(
        ));
        $builder->add('lookingAgedFrom', 'choice', array(
            'empty_value' => '-',
            'choices' => array_combine(range(16, 99), range(16, 99)),
            'required' => false,
        ));
        $builder->add('lookingAgedTo', 'choice', array(
            'empty_value' => '-',
            'choices' => array_combine(range(16, 99), range(16, 99)),
            'required' => false,
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view['lookingFor']->set('label', 'form.search.looking_for');
        $view['city']->set('label', 'form.search.looking_in_city');
        $view['lookingAgedFrom']->set('label', 'form.search.looking_aged.label');
        $view['lookingAgedFrom']->set('empty_value', 'form.search.looking_aged.no_from');
        $view['lookingAgedTo']->set('empty_value', 'form.search.looking_aged.no_to');
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'KurumiMainBundle:Profile',
            'validation_groups' => 'search',
        ));
    }
}