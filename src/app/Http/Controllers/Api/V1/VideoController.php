<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\VideoRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
use App\Resources\Api\V1\VideoResource;
use App\Services\Videos\VideoService;
use App\Services\Videos\VideoUploadService;
use App\Services\Videos\VideoValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VideoController extends Controller
{
    public function __construct(
        private VideoService $videoService,
        private VideoValidationService $videoValidationService,
        private VideoUploadService $videoUploadService,
        private VideoRepository $videoRepository,
    ) {}

    public function index(Request $request): SuccessResponse|ErrorResponse {
        $parameters = $request->only(['scope', 'state', 'status']);

        $videos = VideoResource::collection(
            $this->videoRepository->list(
                $parameters,
                $request->get('page', 1),
                $request->get('limit', 10),
            ),
        );

        return new SuccessResponse(
            __('video.success_list'),
            $videos->toArray($request),
            Response::HTTP_OK,
        );
    }

    public function show(int $videoId): SuccessResponse|ErrorResponse {
        $video = $this->videoRepository->get($videoId);
        if (!$video) {
            return new ErrorResponse(
                [__('video.errors.failed_find')],
                Response::HTTP_NOT_FOUND
            );
        }

        $videoData = new VideoResource($video);
        return new SuccessResponse(
            __('video.success_list'),
            $videoData->toArray(),
            Response::HTTP_OK,
        );
    }

    public function store(Request $request): SuccessResponse|ErrorResponse {
        try {
            /**
             * @var User $user
             */
            $user = Auth::user();
            $input = $request->all();

            $this->videoValidationService->validateCanUpload($user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $uploadRequestErrors = $this->videoValidationService->validateUploadRequest($input);
            if ($uploadRequestErrors) {
                return new ErrorResponse($uploadRequestErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $filename = $this->videoService->generateFilename();
            $stored = $this->videoUploadService->store($request->file, $filename);
            if (!$stored) {
                return new ErrorResponse(
                    [__('video.errors.failed_upload')],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            $originalMeta = $this->videoService->extractMeta($stored);
            if (empty($originalMeta['width'])) {
                return new ErrorResponse(
                    [__('video.errors.failed_meta_extraction')],
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                );
            }

            unset($input['file']);
            $createdVideo = $this->videoRepository->create(
                $input,
                $filename,
                $this->videoService->generateVkey(),
                $originalMeta,
                $user->getAuthIdentifier(),
            );
            if (!$createdVideo) {
                return new ErrorResponse(
                    [__('general.errors.database.failed_insert')],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            return new SuccessResponse(
                __('video.success_save'),
                $createdVideo->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            Log::error('Store video: unexpected error ', [
                'input' => $request->except('file'),
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(Request $request, int $videoId): SuccessResponse|ErrorResponse {
        $input = $request->except(['id', 'vkey', 'filename', '_method']);

        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new ErrorResponse(
                    [__('video.errors.failed_find')],
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->videoValidationService->validatePreConditionsToUpdate($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $requestValidationErrors = $this->videoValidationService->validateUpdateRequest($input);
            if ($requestValidationErrors) {
                return new ErrorResponse(
                    $requestValidationErrors,
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $updatedVideo = $this->videoRepository->updateById($videoId, $input);
            if (!$updatedVideo) {
                return new ErrorResponse(
                    [__('videos.errors.failed_update')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new SuccessResponse(__('video.success_update'), [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Video update: unexpected error', [
                'videoId' => $videoId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function activate(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new ErrorResponse(
                    [__('video.errors.failed_find')],
                    Response::HTTP_NOT_FOUND
                );
            }


            $this->videoValidationService->validatePreConditionsToActivate($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $activatedVideo = $this->videoRepository->activate($video);

            return new SuccessResponse(
                __('video.success_activate'),
                $activatedVideo->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Video activate: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
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
                return new ErrorResponse(
                    [__('video.errors.failed_find')],
                    Response::HTTP_NOT_FOUND
                );
            }


            $this->videoValidationService->validatePreConditionsToDeactivate($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $deactivatedVideo = $this->videoRepository->deactivate($video);

            return new SuccessResponse(
                __('video.success_deactivate'),
                $deactivatedVideo->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Video deactivate: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
