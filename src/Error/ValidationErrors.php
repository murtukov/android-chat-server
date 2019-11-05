<?php

namespace App\Error;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrors
{
    public static function serializeErrors(ConstraintViolationListInterface $violationList): array
    {
        $errors = [];

        foreach ($violationList as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $errors;
    }
}