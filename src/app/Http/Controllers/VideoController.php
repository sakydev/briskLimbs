<?php

namespace App\Http\Controllers;

use App\Services\Videos\VideoService;
use App\Services\Videos\VideoValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function __construct(
        private VideoService $videoService,
        private VideoValidationService $videoValidationService,
    ) {

    }

    public function index()
    {

    }

    public function create(): View
    {
        return view('upload');
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $uploadPermissionsErrors = $this->videoValidationService->validateCanUpload($user);
        if ($uploadPermissionsErrors) {
            return $this->sendErrorJsonResponse($uploadPermissionsErrors);
        }

        $uploadRequestErrors = $this->videoValidationService->validateRequest($request->all());
        if ($uploadRequestErrors) {
            return $this->sendErrorJsonResponse($uploadRequestErrors);
        }
    }
}
