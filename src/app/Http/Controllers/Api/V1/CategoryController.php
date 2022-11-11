<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Resources\Api\V1\CategoryResource;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Services\CategoryValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryValidationService $categoryValidationService,
        private CategoryRepository $categoryRepository,
    ) {}

    public function index(Request $request): SuccessResponse|ErrorResponse {
        $categories = CategoryResource::collection(
            $this->categoryRepository->list(),
        );

        return new SuccessResponse(
            __('category.success.find.list'),
            $categories->toArray($request),
            Response::HTTP_OK,
        );
    }

    public function show(int $categoryId): SuccessResponse|ErrorResponse {
        $category = $this->categoryRepository->get($categoryId);
        if (!$category) {
            return new ErrorResponse(
                [__('category.failed.find.fetch')],
                Response::HTTP_NOT_FOUND
            );
        }

        $categoryData = new CategoryResource($category);
        return new SuccessResponse(
            __('category.success.find.fetch'),
            $categoryData->toArray(),
            Response::HTTP_OK,
        );
    }

    public function store(Request $request): SuccessResponse|ErrorResponse {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $input = $request->all();

        try {
            $this->categoryValidationService->validateCanCreate($user);
            if ($this->categoryValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->categoryValidationService->getErrors(),
                    $this->categoryValidationService->getStatus(),
                );
            }

            $createRequestErrors = $this->categoryValidationService->validateCreateRequest($input);
            if ($createRequestErrors) {
                return new ErrorResponse($createRequestErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $createdCategory = $this->categoryRepository->create($input);
            if (!$createdCategory) {
                return new ErrorResponse(
                    [__('category.failed.store.unknown')],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            return new SuccessResponse(
                __('category.success.store.single'),
                $createdCategory->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            Log::error('Category store: unexpected error ', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('category.failed.store.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(Request $request, int $categoryId): SuccessResponse|ErrorResponse {
        $input = $request->only(['name', 'description']);

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

            $this->categoryValidationService->validateCanUpdate($user);
            if ($this->categoryValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->categoryValidationService->getErrors(),
                    $this->categoryValidationService->getStatus(),
                );
            }

            $updateRequestErrors = $this->categoryValidationService->validateUpdateRequest($input);
            if ($updateRequestErrors) {
                return new ErrorResponse($updateRequestErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updatedCategory = $this->categoryRepository->update($category, $input);
            if (!$updatedCategory) {
                return new ErrorResponse(
                    [__('category.failed.update.unknown')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $categoryData = new CategoryResource($updatedCategory);
            return new SuccessResponse(
                __('category.success.update.single'),
                $categoryData->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            Log::error('Category update: unexpected error', [
                'categoryId' => $categoryId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('category.failed.delete.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy(int $categoryId): SuccessResponse|ErrorResponse {
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

            $this->categoryValidationService->validateCanDelete($user);
            if ($this->categoryValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->categoryValidationService->getErrors(),
                    $this->categoryValidationService->getStatus(),
                );
            }

            $deletedCategory = $this->categoryRepository->delete($category);
            if (!$deletedCategory) {
                return new ErrorResponse(
                    [__('category.failed.delete.unknown')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new SuccessResponse(__('category.success.delete.single'), [],Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error('Category delete: unexpected error', [
                'categoryId' => $categoryId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('category.failed.delete.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
