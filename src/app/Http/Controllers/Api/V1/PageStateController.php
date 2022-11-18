<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\PageRepository;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\ExceptionErrorResponse;
use App\Resources\Api\V1\Responses\NotFoundErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Services\PageValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                return new NotFoundErrorResponse('page.failed.find.fetch');
            }


            $this->pageValidationService->validatePreConditionsToPublish($page, $user);
            if ($this->pageValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->pageValidationService->getErrors(),
                    $this->pageValidationService->getStatus(),
                );
            }

            $publishedPage = $this->pageRepository->publish($page);

            return new SuccessResponse('page.success.update.publish', $publishedPage->toArray());
        } catch (Throwable $exception) {
            Log::error('Page publish: unexpected error', [
                'pageId' => $pageId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('page.failed.update.unknown');
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
                return new NotFoundErrorResponse('page.failed.find.fetch');
            }


            $this->pageValidationService->validatePreConditionsToUnpublish($page, $user);
            if ($this->pageValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->pageValidationService->getErrors(),
                    $this->pageValidationService->getStatus(),
                );
            }

            $unpublishedPage = $this->pageRepository->unPublish($page);

            return new SuccessResponse('page.success.update.unpublish', $unpublishedPage->toArray());
        } catch (Throwable $exception) {
            Log::error('Page unpublish: unexpected error', [
                'pageId' => $videoId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('page.failed.update.unknown');
        }
    }
}
