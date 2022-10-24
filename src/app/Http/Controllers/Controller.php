<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendErrorResponseJSON(array $errors): JsonResponse
    {
        return new JsonResponse(['success' => false, 'errors' => $errors, 'data' => []]);
    }

    public function sendSuccessResponseJSON(string $message, mixed $data): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'errors' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
