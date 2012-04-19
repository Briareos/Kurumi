<?php

namespace App\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TimezoneValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constrain for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @api
     */
    function isValid($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return true;
        }

        try {
            new \DateTimeZone($value);
        } catch (\Exception $e) {
            $this->context->addViolation($constraint->message, array(
                '{{ value }}' => $value,
            ));
        }
    }


}