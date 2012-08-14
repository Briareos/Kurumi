<?php

namespace Kurumi\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MinAgeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTime) {
            return;
        }
        $today = new \DateTime('now');
        $age = $today->diff($value);
        if ($age->y < $constraint->limit) {
            $this->context->addViolation($constraint->message, array('{{ limit }}' => $constraint->limit));
        }
    }


}