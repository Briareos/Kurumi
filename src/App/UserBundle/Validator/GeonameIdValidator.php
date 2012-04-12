<?php

namespace App\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GeonameIdValidator extends ConstraintValidator {

    /**
     * @var \App\UserBundle\Util\GeonameLookup
     */
    private $geonameLookup;

    public function __construct($geonameLookup) {
        $this->geonameLookup = $geonameLookup;
    }

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
        return (bool)$this->geonameLookup->get($value);
    }


}