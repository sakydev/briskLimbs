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

    public function sendErrorJsonResponse(mixed $data): JsonResponse
    {
        return new JsonResponse(['success' => false, 'errors' => $data, 'data' => []]);
    }

    public function sendSuccessJsonResponse(mixed $data): JsonResponse
    {
        return new JsonResponse(['success' => true, 'errors' => false, 'data' => $data]);
    }
}
