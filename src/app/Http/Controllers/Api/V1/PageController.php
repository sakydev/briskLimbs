<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\PageRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\PageResource;
use App\Resources\Api\V1\SuccessResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    public function __construct(
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
}
