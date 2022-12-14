<?php

namespace App\Resources\Api\V1\Responses;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct(
        string $message,
        array $data = [],
        int $status = 200,
        array $headers = [],
        int $options = 0,
    ) {
        parent::__construct(
            [
                'success' => true,
                'messages' => __($message),
                'data' => $data,
            ],
            $status,
            $headers,
            $options,
        );
    }
}

