<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\PageRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\PageResource;
use App\Resources\Api\V1\SuccessResponse;
use App\Services\PageValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PageController extends Controller
{
    public function __construct(
        private PageValidationService $pageValidationService,
        private PageRepository $pageRepository,
    ) {}

    public function index(Request $request): SuccessResponse|ErrorResponse {
        $pages = PageResource::collection(
            $this->pageRepository->list(
                [],
                $request->get('page', 1),
                $request->get('limit', 10),
            ),
        );

        return new SuccessResponse(
            __('page.success.find.list'),
            $pages->toArray($request),
            Response::HTTP_OK,
        );
    }

    public function show(int $pageId): SuccessResponse|ErrorResponse {
        $page = $this->pageRepository->get($pageId);
        if (!$page) {
            return new ErrorResponse(
                [__('page.failed.find.fetch')],
                Response::HTTP_NOT_FOUND
            );
        }

        $pageData = new PageResource($page);
        return new SuccessResponse(
            __('page.success.find.fetch'),
            $pageData->toArray(),
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
            $this->pageValidationService->validateCanCreate($user);
            if ($this->pageValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->pageValidationService->getErrors(),
                    $this->pageValidationService->getStatus(),
                );
            }

            $createRequestErrors = $this->pageValidationService->validateCreateRequest($input);
            if ($createRequestErrors) {
                return new ErrorResponse($createRequestErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $createdPage = $this->pageRepository->create($input);
            if (!$createdPage) {
                return new ErrorResponse(
                    [__('page.failed.store.unknown')],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            return new SuccessResponse(
                __('page.success.store.single'),
                $createdPage->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            Log::error('Store page: unexpected error ', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('page.failed.store.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(Request $request, int $pageId): SuccessResponse|ErrorResponse {
        $input = $request->only(['title', 'slug', 'content']);

        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $page = $this->pageRepository->get($pageId);
            if (!$page) {
                return new ErrorResponse(
                    [__('page.failed.find.fetch')],
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->pageValidationService->validateCanUpdate($user);
            if ($this->pageValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->pageValidationService->getErrors(),
                    $this->pageValidationService->getStatus(),
                );
            }

            $updateRequestErrors = $this->pageValidationService->validateUpdateRequest($input);
            if ($updateRequestErrors) {
                return new ErrorResponse($updateRequestErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updatedPage = $this->pageRepository->update($page, $input);
            if (!$updatedPage) {
                return new ErrorResponse(
                    [__('page.failed.update.unknown')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $pageData = new PageResource($updatedPage);
            return new SuccessResponse(
                __('page.success.update.single'),
                $pageData->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Page update: unexpected error', [
                'pageId' => $pageId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('page.failed.delete.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy(int $pageId): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
            $page = $this->pageRepository->get($pageId);
            if (!$page) {
                return new ErrorResponse(
                    [__('page.failed.find.fetch')],
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->pageValidationService->validateCanDelete($user);
            if ($this->pageValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->pageValidationService->getErrors(),
                    $this->pageValidationService->getStatus(),
                );
            }

            $deletedPage = $this->pageRepository->delete($page);
            if (!$deletedPage) {
                return new ErrorResponse(
                    [__('page.failed.delete.unknown')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new SuccessResponse(__('page.success.delete.single'), [],Response::HTTP_OK);
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Page delete: unexpected error', [
                'pageId' => $pageId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('page.failed.delete.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
