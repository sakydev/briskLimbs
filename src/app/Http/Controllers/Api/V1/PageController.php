<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\PageRepository;
use App\Resources\Api\V1\PageResource;
use App\Resources\Api\V1\Responses\BadRequestErrorResponse;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\ExceptionErrorResponse;
use App\Resources\Api\V1\Responses\NotFoundErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Resources\Api\V1\Responses\UnprocessableRquestErrorResponse;
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
                $request->get('limit', config('settings.max_results_page')),
            ),
        );

        return new SuccessResponse('page.success.find.list', $pages->toArray($request));
    }

    public function show(int $pageId): SuccessResponse|ErrorResponse {
        $page = $this->pageRepository->get($pageId);
        if (!$page) {
            return new NotFoundErrorResponse('page.failed.find.fetch');
        }

        $pageData = new PageResource($page);
        return new SuccessResponse('page.success.find.fetch', $pageData->toArray());
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
                return new UnprocessableRquestErrorResponse($createRequestErrors);
            }

            $createdPage = $this->pageRepository->create($input);
            if (!$createdPage) {
                return new BadRequestErrorResponse('page.failed.store.unknown');
            }

            return new SuccessResponse('page.success.store.single', $createdPage->toArray());
        } catch (Throwable $exception) {
            Log::error('Store page: unexpected error ', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('page.failed.store.unknown');
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
                return new NotFoundErrorResponse('page.failed.find.fetch');
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
                return new BadRequestErrorResponse($updateRequestErrors);
            }

            $updatedPage = $this->pageRepository->update($page, $input);
            if (!$updatedPage) {
                return new BadRequestErrorResponse('page.failed.update.unknown');
            }

            $pageData = new PageResource($updatedPage);
            return new SuccessResponse('page.success.update.single', $pageData->toArray());
        } catch (Throwable $exception) {
            Log::error('Page update: unexpected error', [
                'pageId' => $pageId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('page.failed.update.unknown');
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
                return new NotFoundErrorResponse('page.failed.find.fetch');
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
                return new BadRequestErrorResponse('page.failed.delete.unknown');
            }

            return new SuccessResponse(__('page.success.delete.single'), [],Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error('Page delete: unexpected error', [
                'pageId' => $pageId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('page.failed.delete.unknown');
        }
    }
}
