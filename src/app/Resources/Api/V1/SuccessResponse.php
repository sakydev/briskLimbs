<?php

namespace App\Resources\Api\V1;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct(string $message, array $data, int $status, array $headers = [], int $options = 0)
    {
        parent::__construct(
            [
                'success' => true,
                'messages' => $message,
                'data' => $data,
            ],
            $status,
            $headers,
            $options,
        );
    }
}

