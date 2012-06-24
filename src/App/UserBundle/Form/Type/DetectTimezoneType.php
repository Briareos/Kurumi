<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DetectTimezoneType extends AbstractType
{

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'detect_timezone';
    }


}