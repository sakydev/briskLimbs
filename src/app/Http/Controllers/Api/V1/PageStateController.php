<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\PageRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
use App\Services\PageValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PageStateController extends Controller
{
    public function __construct(
        private PageValidationService $pageValidationService,
        private PageRepository $pageRepository,
    ) {}

    public function publish(int $pageId): SuccessResponse|ErrorResponse {
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


            $this->pageValidationService->validatePreConditionsToPublish($page, $user);
            if ($this->pageValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->pageValidationService->getErrors(),
                    $this->pageValidationService->getStatus(),
                );
            }

            $publishedPage = $this->pageRepository->publish($page);

            return new SuccessResponse(
                __('page.success.update.publish'),
                $publishedPage->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Page publish: unexpected error', [
                'pageId' => $pageId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('page.failed.update.unknown')],
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
            $page = $this->pageRepository->get($videoId);
            if (!$page) {
                return new ErrorResponse(
                    [__('page.errors.failed_find')],
                    Response::HTTP_NOT_FOUND
                );
            }


            $this->pageValidationService->validatePreConditionsToUnpublish($page, $user);
            if ($this->pageValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->pageValidationService->getErrors(),
                    $this->pageValidationService->getStatus(),
                );
            }

            $unpublishedPage = $this->pageRepository->unpublish($page);

            return new SuccessResponse(
                __('page.success.update.unpublish'),
                $unpublishedPage->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Page unpublish: unexpected error', [
                'pageId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('page.failed.update.unknown')],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
