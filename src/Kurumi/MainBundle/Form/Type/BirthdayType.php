<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType as BaseBirthdayType;

class BirthdayType extends BaseBirthdayType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'empty_value' => array(
                'year' => 'Year',
                'month' => 'Month',
                'day' => 'Day'
            ),
            'years' => range(date('Y'), date('Y') - 100),
        ));
    }
}