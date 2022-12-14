<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Resources\Api\V1\CategoryResource;
use App\Resources\Api\V1\Responses\BadRequestErrorResponse;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\ExceptionErrorResponse;
use App\Resources\Api\V1\Responses\NotFoundErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Resources\Api\V1\Responses\UnprocessableRquestErrorResponse;
use App\Services\CategoryValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        return new SuccessResponse('category.success.find.list', $categories->toArray($request));
    }

    public function show(int $categoryId): SuccessResponse|ErrorResponse {
        $category = $this->categoryRepository->get($categoryId);
        if (!$category) {
            return new NotFoundErrorResponse('category.failed.find.fetch');
        }

        $categoryData = new CategoryResource($category);
        return new SuccessResponse('category.success.find.fetch', $categoryData->toArray());
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
                return new UnprocessableRquestErrorResponse($createRequestErrors);
            }

            $createdCategory = $this->categoryRepository->create($input);
            if (!$createdCategory) {
                return new BadRequestErrorResponse('category.failed.store.unknown');
            }

            return new SuccessResponse('category.success.store.single', $createdCategory->toArray());
        } catch (Throwable $exception) {
            Log::error('Category store: unexpected error ', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('category.failed.store.unknown');
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
                return new NotFoundErrorResponse('category.failed.find.fetch');
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
                return new BadRequestErrorResponse($updateRequestErrors);
            }

            $updatedCategory = $this->categoryRepository->update($category, $input);
            if (!$updatedCategory) {
                return new BadRequestErrorResponse('category.failed.update.unknown');
            }

            $categoryData = new CategoryResource($updatedCategory);
            return new SuccessResponse('category.success.update.single', $categoryData->toArray());
        } catch (Throwable $exception) {
            Log::error('Category update: unexpected error', [
                'categoryId' => $categoryId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('category.failed.update.unknown');
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
                return new NotFoundErrorResponse('category.failed.find.fetch');
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
                return new BadRequestErrorResponse('category.failed.delete.unknown');
            }

            return new SuccessResponse('category.success.delete.single');
        } catch (Throwable $exception) {
            Log::error('Category delete: unexpected error', [
                'categoryId' => $categoryId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('category.failed.delete.unknown');
        }
    }
}
