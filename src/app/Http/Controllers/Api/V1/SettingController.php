<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
use App\Services\Users\UserValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    public function __construct(
        private UserValidationService $userValidationService,
        private SettingRepository $settingRepository,
    ) {}

    public function update(Request $request): SuccessResponse|ErrorResponse {
        /**
         * @var User $user;
         */
        $user = Auth::user();
        $input = $request->all();

        $this->userValidationService->validateCanUpdateSettings($user);
        if ($this->userValidationService->hasErrors()) {
            return new ErrorResponse(
                $this->userValidationService->getErrors(),
                $this->userValidationService->getStatus(),
            );
        }

        $updated = $this->settingRepository->update($request->get('name'), $request->get('value'));
        if (!$updated) {
            return new ErrorResponse(
                [__('user.errors.failed_update_settings')],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new SuccessResponse(__('video.success_update'), [], Response::HTTP_OK);
    }
}
