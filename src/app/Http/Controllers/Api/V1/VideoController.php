<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\VideoProcessing;
use App\Models\User;
use App\Repositories\VideoRepository;
use App\Resources\Api\V1\Responses\BadRequestErrorResponse;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\ExceptionErrorResponse;
use App\Resources\Api\V1\Responses\NotFoundErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Resources\Api\V1\Responses\UnprocessableRquestErrorResponse;
use App\Resources\Api\V1\VideoResource;
use App\Services\Videos\VideoService;
use App\Services\Videos\VideoUploadService;
use App\Services\Videos\VideoValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                $request->get('limit', config('settings.max_results_video')),
            ),
        );

        return new SuccessResponse('video.success.find.list', $videos->toArray($request));
    }

    public function show(int $videoId): SuccessResponse|ErrorResponse {
        $video = $this->videoRepository->get($videoId);
        if (!$video) {
            return new NotFoundErrorResponse('video.failed.find.fetch');
        }

        $videoData = new VideoResource($video);
        return new SuccessResponse('video.success.find.fetch', $videoData->toArray());
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
                return new UnprocessableRquestErrorResponse($uploadRequestErrors);
            }

            $filename = $this->videoService->generateFilename();
            $stored = $this->videoUploadService->store($request->file, $filename);
            if (!$stored) {
                return new BadRequestErrorResponse('video.failed.store.file');
            }

            $originalMeta = $this->videoService->extractMeta($stored);
            if (empty($originalMeta['width'])) {
                return new BadRequestErrorResponse('video.failed.store.meta');
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
                return new BadRequestErrorResponse('video.failed.store.unknown');
            }

            VideoProcessing::dispatch($createdVideo);

            return new SuccessResponse('video.success.store.single', $createdVideo->toArray());
        } catch (Throwable $exception) {
            Log::error('Store video: unexpected error ', [
                'input' => $request->except('file'),
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('videos.failed.store.unknown');
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
                return new NotFoundErrorResponse('video.failed.find.fetch');
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
                return new UnprocessableRquestErrorResponse($requestValidationErrors);
            }

            $updatedVideo = $this->videoRepository->update($video, $input);
            if (!$updatedVideo) {
                return new BadRequestErrorResponse('videos.failed.update.unknown');
            }

            $videoData = new VideoResource($updatedVideo);
            return new SuccessResponse('video.success.update.single', $videoData->toArray());
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Video update: unexpected error', [
                'videoId' => $videoId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('videos.failed.update.unknown');
        }
    }

    public function delete(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }

            $this->videoValidationService->validatePreConditionsToDelete($video, $user);
            if ($this->videoValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->videoValidationService->getErrors(),
                    $this->videoValidationService->getStatus(),
                );
            }

            $deletedMedia = $this->videoService->deleteMedia($video);
            if (!$deletedMedia) {
                return new BadRequestErrorResponse('video.failed.delete.media');
            }

            $deletedVideo = $this->videoRepository->delete($video);
            if (!$deletedVideo) {
                return new BadRequestErrorResponse('videos.failed.delete.unknown');
            }

            return new SuccessResponse('video.success.delete.single');
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Video delete: unexpected error', [
                'videoId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('videos.failed.delete.unknown');
        }
    }
}
