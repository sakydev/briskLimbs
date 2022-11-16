<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\VideoRepository;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\ExceptionErrorResponse;
use App\Resources\Api\V1\Responses\NotFoundErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Services\Videos\VideoValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class VideoStateController extends Controller
{
    public function __construct(
        private VideoValidationService $videoValidationService,
        private VideoRepository $videoRepository,
    ) {}

    public function activate(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }


            $this->videoValidationService->validatePreConditionsToActivate($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $activatedVideo = $this->videoRepository->activate($video);

            return new SuccessResponse('video.success.update.activate', $activatedVideo->toArray());
        } catch (Throwable $exception) {
            Log::error('Video activate: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('video.failed.update.unknown');
        }
    }

    public function deactivate(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }


            $this->videoValidationService->validatePreConditionsToDeactivate($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $deactivatedVideo = $this->videoRepository->deactivate($video);

            return new SuccessResponse('video.success.update.deactivate', $deactivatedVideo->toArray());
        } catch (Throwable $exception) {
            Log::error('Video deactivate: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('video.failed.update.unknown');
        }
    }
}
