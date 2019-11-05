<?php

namespace App\Response;

use App\Error\ValidationErrors;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorsResponse extends JsonResponse
{
    public function __construct(ConstraintViolationListInterface $errors)
    {
        parent::__construct([
            'message' => 'Some of the provided fields have invalid data.',
            'errors' => ValidationErrors::serializeErrors($errors)
        ], Response::HTTP_BAD_REQUEST);
    }
}