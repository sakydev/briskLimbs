<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CommentRepository;
use App\Repositories\VideoRepository;
use App\Resources\Api\V1\CommentResource;
use App\Resources\Api\V1\Responses\BadRequestErrorResponse;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\ExceptionErrorResponse;
use App\Resources\Api\V1\Responses\NotFoundErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Resources\Api\V1\Responses\UnprocessableRquestErrorResponse;
use App\Services\CommentValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CommentController extends Controller
{
    public function __construct(
        private CommentValidationService $commentValidationService,
        private VideoRepository $videoRepository,
        private CommentRepository $commentRepository,
    ) {}

    public function index(Request $request, int $videoId): SuccessResponse|ErrorResponse {
        $video = $this->videoRepository->get($videoId);
        if (!$video) {
            return new NotFoundErrorResponse('video.failed.find.fetch');
        }

        $comments = CommentResource::collection(
            $this->commentRepository->list(
                [],
                $request->get('page', 1),
                $request->get('limit', config('settings.max_results_comment')),
            ),
        );

        return new SuccessResponse('comment.success.find.list', $comments->toArray($request));
    }

    public function show(int $videoId, int $commentId): SuccessResponse|ErrorResponse {
        $video = $this->videoRepository->get($videoId);
        if (!$video) {
            return new NotFoundErrorResponse('video.failed.find.fetch');
        }

        $comment = $this->commentRepository->get($commentId);
        if (!$comment) {
            return new NotFoundErrorResponse('comment.failed.find.fetch');
        }

        $commentData = new CommentResource($comment);
        return new SuccessResponse('comment.success.find.fetch', $commentData->toArray());
    }

    public function store(Request $request, int $videoId): SuccessResponse|ErrorResponse {
        $input = $request->only('content');

        /**
         * @var User $user
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }

            $this->commentValidationService->validateCanCreate($user, $video);
            if ($this->commentValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->commentValidationService->getErrors(),
                    $this->commentValidationService->getStatus(),
                );
            }

            $createRequestErrors = $this->commentValidationService->validateCreateRequest($input);
            if ($createRequestErrors) {
                return new UnprocessableRquestErrorResponse($createRequestErrors);
            }

            $createdComment = $this->commentRepository->create(
                $input,
                $user->getAuthIdentifier(),
                $videoId,
            );
            if (!$createdComment) {
                return new BadRequestErrorResponse('comment.failed.store.unknown');
            }

            return new SuccessResponse('comment.success.store.single', $createdComment->toArray());
        } catch (Throwable $exception) {
            Log::error('Store comment: unexpected error ', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('comment.failed.delete.unknown');
        }
    }

    public function update(Request $request, int $videoId, int $commentId): SuccessResponse|ErrorResponse {
        $input = $request->only('content');

        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }

            $comment = $this->commentRepository->get($commentId);
            if (!$comment) {
                return new NotFoundErrorResponse('comment.failed.find.fetch');
            }

            $this->commentValidationService->validateCanUpdate($user, $video, $comment);
            if ($this->commentValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->commentValidationService->getErrors(),
                    $this->commentValidationService->getStatus(),
                );
            }

            $updateRequestErrors = $this->commentValidationService->validateUpdateRequest($input);
            if ($updateRequestErrors) {
                return new UnprocessableRquestErrorResponse($updateRequestErrors);
            }

            $updatedComment = $this->commentRepository->update($comment, $input);
            if (!$updatedComment) {
                return new BadRequestErrorResponse('comment.failed.store.unknown');
            }

            $commentData = new CommentResource($updatedComment);
            return new SuccessResponse('comment.success.update.single', $commentData->toArray());
        } catch (Throwable $exception) {
            Log::error('Comment update: unexpected error', [
                'commentId' => $commentId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('comment.failed.delete.unknown');
        }
    }

    public function destroy(int $videoId, int $commentId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $video = $this->videoRepository->get($videoId);
            if (!$video) {
                return new NotFoundErrorResponse('video.failed.find.fetch');
            }

            $comment = $this->commentRepository->get($commentId);
            if (!$comment) {
                return new NotFoundErrorResponse('comment.failed.find.fetch');
            }

            $this->commentValidationService->validateCanDelete($user, $video, $comment);
            if ($this->commentValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->commentValidationService->getErrors(),
                    $this->commentValidationService->getStatus(),
                );
            }

            $deletedComment = $this->commentRepository->delete($comment);
            if (!$deletedComment) {
                return new BadRequestErrorResponse('comment.failed.store.unknown');
            }

            return new SuccessResponse(__('comment.success.delete.single'), [],Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error('Page delete: unexpected error', [
                'commentId' => $commentId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('comment.failed.delete.unknown');
        }
    }
}
