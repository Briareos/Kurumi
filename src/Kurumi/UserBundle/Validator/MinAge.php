<?php

namespace Kurumi\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

class MinAge extends Constraint {

    public $message = 'You must be at least {{ limit }} years of age to use this website.';
    public $limit;

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'limit';
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('limit');
    }
}