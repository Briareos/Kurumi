<?php

namespace Kurumi\MainBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MinAgeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /** @var $constraint MinAge */
        if (!$value instanceof \DateTime) {
            return;
        }
        $today = new \DateTime('now');
        $age = $today->diff($value);
        if ($age->y < $constraint->limit) {
            $this->context->addViolation($constraint->message, ['{{ limit }}' => $constraint->limit]);
        }
    }


}
