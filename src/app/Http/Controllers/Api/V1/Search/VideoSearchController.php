<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Http\Controllers\Controller;
use App\Repositories\VideoRepository;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Resources\Api\V1\VideoResource;
use App\Services\Videos\VideoValidationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoSearchController extends Controller
{
    public function __construct(
      private VideoValidationService $videoValidationService,
      private VideoRepository $videoRepository,
    ) {}

    public function search(Request $request): SuccessResponse|ErrorResponse {
        $input = $request->only('query');
        $query = $request->get('query');

        $searchErrors = $this->videoValidationService->validateSearchRequest($input);
        if ($searchErrors) {
            return new ErrorResponse(
                $searchErrors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $videos = VideoResource::collection(
            $this->videoRepository->search(
                $query,
                $request->get('page', 1),
                $request->get('limit', config('settings.max_results_video')),
            ),
        );

        return new SuccessResponse(
            __('video.success.find.search'),
            $videos->toArray($request),
            Response::HTTP_OK,
        );

    }
}
