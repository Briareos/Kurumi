<?php

namespace App\ UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class GenderType extends AbstractType
{
    public function getDefaultOptions()
    {
        return array(
            'choices' => array(
                1 => 'Male',
                2 => 'Female',
            )
        );
    }

    public function getParent(array $options)
    {
        return 'choice';
    }

    public function getName()
    {
        return 'gender';
    }
}