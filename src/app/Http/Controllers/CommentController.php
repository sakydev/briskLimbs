<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\CommentRepository;
use App\Repositories\VideoRepository;
use App\Resources\Api\V1\CommentResource;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
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
            return new ErrorResponse(
                [__('video.failed.find.fetch')],
                Response::HTTP_NOT_FOUND
            );
        }

        $comments = CommentResource::collection(
            $this->commentRepository->list(
                [],
                $request->get('page', 1),
                $request->get('limit', 10),
            ),
        );

        return new SuccessResponse(
            __('comment.success.find.list'),
            $comments->toArray($request),
            Response::HTTP_OK,
        );
    }

    public function show(int $videoId, int $commentId): SuccessResponse|ErrorResponse {

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
                return new ErrorResponse(
                    [__('video.failed.find.fetch')],
                    Response::HTTP_NOT_FOUND
                );
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
                return new ErrorResponse($createRequestErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $createdComment = $this->commentRepository->create(
                $input,
                $user->getAuthIdentifier(),
                $videoId,
            );
            if (!$createdComment) {
                return new ErrorResponse(
                    [__('comment.failed.store.unknown')],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            return new SuccessResponse(
                __('comment.success.store.single'),
                $createdComment->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            Log::error('Store comment: unexpected error ', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('comment.failed.store.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(int $videoId, int $commentId): SuccessResponse|ErrorResponse {

    }

    public function delete(int $videoId, int $commentId): SuccessResponse|ErrorResponse {

    }
}
