<?php

namespace App\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Timezone extends Constraint {

    public $message = "The chosen timezone ({{ value }}) is invalid.";

    public function validatedBy() {
        return 'validator.timezone';
    }

}