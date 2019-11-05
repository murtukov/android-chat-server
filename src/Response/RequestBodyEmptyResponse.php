<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RequestBodyEmptyResponse extends JsonResponse
{
    public function __construct()
    {
        parent::__construct([
            'message' => 'You must provide input data'
        ], Response::HTTP_BAD_REQUEST);
    }
}