<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\VideoRepository;
use App\Services\Videos\VideoService;
use App\Services\Videos\VideoUploadService;
use App\Services\Videos\VideoValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $supportedVideoFormats = config('settings.supported_formats_video');

        $iniMaxFilesize = convertToMB(ini_get('upload_max_filesize'));
        $configMaxFilesize = convertToMB(config('settings.max_filesize_video'));
        $maxFilesizeInMB = min($iniMaxFilesize, $configMaxFilesize);

        return view('upload', compact('maxFilesizeInMB', 'supportedVideoFormats'));
    }

    public function store(Request $request): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $input = $request->all();

        $uploadPermissionsErrors = $this->videoValidationService->validateCanUpload($user);
        if ($uploadPermissionsErrors) {
            return $this->sendErrorResponseJSON($uploadPermissionsErrors);
        }

        $uploadRequestErrors = $this->videoValidationService->validateUploadRequest($input);
        if ($uploadRequestErrors) {
            return $this->sendErrorResponseJSON($uploadRequestErrors);
        }

        $filename = $this->videoService->generateFilename();
        $vkey = $this->videoService->generateVkey();

        $stored = $this->videoUploadService->store($request->file, $filename);
        if (!$stored) {
            return $this->sendErrorResponseJSON([__('video.errors.failed_upload')]);
        }

        $created = $this->videoRepository->create($input, $vkey, $filename, $user->getAuthIdentifier());
        if (!$created) {
            return $this->sendErrorResponseJSON([__('general.errors.database.failed_insert')]);
        }

        return $this->sendSuccessJsonResponse(__('video.success_save'), [
            'id' => $created['id'],
            'vkey' => $created['vkey'],
            'filename' => $created['filename'],
        ]);
    }

    public function update(Request $request, int $videoId): JsonResponse|RedirectResponse
    {
        $responseType = $request->getContentType() === 'json' ? 'json' : 'redirect';

        /**
         * @var User $user
         */
        $user = Auth::user();
        $input = $request->all();

        $updatePermissionsErrors = $this->videoValidationService->validateCanUpdate($user);
        if ($updatePermissionsErrors) {
            return $this->sendErrorResponseJSON($updatePermissionsErrors);
        }

        $updateRequestErrors = $this->videoValidationService->validateUpdateRequest($input);
        if ($updateRequestErrors) {
            return $this->sendErrorResponseJSON($updateRequestErrors);
        }

        unset($input['_token']);
        $updated = $this->videoRepository->updateById($input, $videoId);
        if (!$updated) {
            return $this->sendErrorResponseJSON([__('general.errors.database.failed_update')]);
        }

        return $this->sendSuccessJsonResponse(__('video.success_update'), []);
    }
}
