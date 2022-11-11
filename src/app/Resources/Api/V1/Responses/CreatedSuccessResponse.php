<?php

namespace App\Resources\Api\V1\Responses;

use Symfony\Component\HttpFoundation\Response;

class CreatedSuccessResponse extends SuccessResponse
{
    public function __construct(
        string $message,
        array $data = [],
        array $headers = [],
        int $options = 0,
    ) {

        parent::__construct($message, $data, Response::HTTP_CREATED, $headers, $options);
    }
}

