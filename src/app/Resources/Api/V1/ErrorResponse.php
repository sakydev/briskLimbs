<?php

namespace App\Resources\Api\V1;

use Illuminate\Http\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct(array $errors, int $status, array $headers = [], int $options = 0)
    {
        parent::__construct(
            [
                'error' => true,
                'messages' => $errors,
            ],
            $status,
            $headers,
            $options,
        );
    }
}

