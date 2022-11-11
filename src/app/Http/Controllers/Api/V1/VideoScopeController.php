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
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VideoScopeController extends Controller
{
    public function __construct(
        private VideoValidationService $videoValidationService,
        private VideoRepository $videoRepository,
    ) {}

    public function public(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }


            $this->videoValidationService->validatePreConditionsToMakePublic($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $publicVideo = $this->videoRepository->makePublic($video);

            return new SuccessResponse('video.success.update.public', $publicVideo->toArray());
        } catch (Throwable $exception) {
            Log::error('Video make public: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('video.failed.update.unknown');
        }
    }

    public function private(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }


            $this->videoValidationService->validatePreConditionsToMakePrivate($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $privateVideo = $this->videoRepository->makePrivate($video);

            return new SuccessResponse('video.success.update.private', $privateVideo->toArray());
        } catch (Throwable $exception) {
            Log::error('Video make private: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('video.failed.update.unknown');
        }
    }

    public function unlisted(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }


            $this->videoValidationService->validatePreConditionsToMakeUnlisted($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $unlistedVideo = $this->videoRepository->makeUnlisted($video);

            return new SuccessResponse('video.success.update.unlisted', $unlistedVideo->toArray());
        } catch (Throwable $exception) {
            Log::error('Video make unlisted: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('video.failed.update.unknown');
        }
    }
}
