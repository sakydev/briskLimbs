<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Services\CategoryValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryStateController extends Controller
{
    public function __construct(
        private CategoryValidationService $categoryValidationService,
        private CategoryRepository $categoryRepository,
    ) {}

    public function publish(int $categoryId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $category = $this->categoryRepository->get($categoryId);
            if (!$category) {
                return new ErrorResponse(
                    [__('category.failed.find.fetch')],
                    Response::HTTP_NOT_FOUND
                );
            }


            $this->categoryValidationService->validatePreConditionsToPublish($category, $user);
            if ($this->categoryValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->categoryValidationService->getErrors(),
                    $this->categoryValidationService->getStatus(),
                );
            }

            $publishedCategory = $this->categoryRepository->publish($category);

            return new SuccessResponse(
                __('category.success.update.publish'),
                $publishedCategory->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Category publish: unexpected error', [
                'categoryId' => $categoryId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('category.failed.update.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function unpublish(int $videoId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $category = $this->categoryRepository->get($videoId);
            if (!$category) {
                return new ErrorResponse(
                    [__('category.failed.find.fetch')],
                    Response::HTTP_NOT_FOUND
                );
            }


            $this->categoryValidationService->validatePreConditionsToUnpublish($category, $user);
            if ($this->categoryValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->categoryValidationService->getErrors(),
                    $this->categoryValidationService->getStatus(),
                );
            }

            $unpublishedCategory = $this->categoryRepository->unpublish($category);

            return new SuccessResponse(
                __('category.success.update.unpublish'),
                $unpublishedCategory->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Category unpublish: unexpected error', [
                'categoryId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('category.failed.update.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
