<?php

namespace App\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

class GeonameId extends Constraint {

    public $message = "The given geoname ID is invalid.";

    public function validatedBy() {
        return 'validator.geoname_id';
    }
}