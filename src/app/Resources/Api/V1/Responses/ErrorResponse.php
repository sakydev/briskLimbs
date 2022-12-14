<?php

namespace App\Resources\Api\V1\Responses;

use Illuminate\Http\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct(array|string $error, int $status, array $headers = [], int $options = 0)
    {
        parent::__construct(
            [
                'error' => true,
                'messages' => is_array($error) ? $error : [__($error)],
            ],
            $status,
            $headers,
            $options,
        );
    }
}

