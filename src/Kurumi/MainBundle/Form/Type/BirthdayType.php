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
        $resolver->setDefaults([
            'empty_value' => [
                'year' => 'Year',
                'month' => 'Month',
                'day' => 'Day'
            ],
            'years' => range(date('Y'), date('Y') - 100),
        ]);
    }
}
