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
        dd('asd');
        $pages = PageResource::collection(
            $this->pageRepository->list(
                [],
                $request->get('page', 1),
                $request->get('limit', 10),
            ),
        );

        dd($pages);

        return new SuccessResponse(
            __('general.pages.success.find.list'),
            $pages->toArray($request),
            Response::HTTP_OK,
        );
    }
}
