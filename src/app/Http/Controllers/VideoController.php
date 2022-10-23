<?php

namespace App\Http\Controllers;

use App\Repositories\VideoRepository;
use App\Services\Videos\VideoService;
use App\Services\Videos\VideoUploadService;
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
        private VideoUploadService $videoUploadService,
        private VideoRepository $videoRepository,
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
        $input = $request->all();

        $uploadPermissionsErrors = $this->videoValidationService->validateCanUpload($user);
        if ($uploadPermissionsErrors) {
            return $this->sendErrorJsonResponse($uploadPermissionsErrors);
        }

        $uploadRequestErrors = $this->videoValidationService->validateRequest($input);
        if ($uploadRequestErrors) {
            return $this->sendErrorJsonResponse($uploadRequestErrors);
        }

        $filename = $this->videoService->generateFilename();
        $vkey = $this->videoService->generateVkey();

        $stored = $this->videoUploadService->store($request->file, $filename);
        if (!$stored) {
            return $this->sendErrorJsonResponse(['title' => 'Failed to store', 'description' => '']);
        }

        $created = $this->videoRepository->create(
            $input,
            $vkey,
            $filename,
            $user->getAuthIdentifier(),
        );

        if (!$created) {
            return $this->sendErrorJsonResponse(['title' => 'Failed to save video in database', 'description' => '']);
        }

        return $this->sendSuccessJsonResponse('Video saved successfully', [
            'id' => $created['id'],
            'vkey' => $created['vkey'],
            'filename' => $created['filename'],
        ]);
    }
}
