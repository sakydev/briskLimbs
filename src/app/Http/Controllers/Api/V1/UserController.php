<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
use App\Services\Users\UserValidationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private UserValidationService $userValidationService,
        private UserRepository $userRepository,
    ) {}
    public function show(int $userId): UserResource|ErrorResponse {
        $user = $this->userRepository->get($userId);
        if (!$user) {
            return new ErrorResponse([__('user.errors.failed_find')], Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user, __('user.success_find'));
    }

    public function update(Request $request, int $userId): SuccessResponse|ErrorResponse {
        $input = $request->except(['id', 'username', 'email', '_method']);

        /**
         * @var User $user;
         */
        $user = Auth::user();

        $this->userValidationService->validatePreConditionsToUpdate($userId, $user);
        if ($this->userValidationService->hasErrors()) {
            return new ErrorResponse(
                $this->userValidationService->getErrors(),
                $this->userValidationService->getStatus(),
            );
        }

        $requestValidationErrors = $this->userValidationService->validateUpdateRequest($input);
        if ($requestValidationErrors) {
            return new ErrorResponse(
                $requestValidationErrors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $updatedUser = $this->userRepository->updateById($userId, $input);
        if (!$updatedUser) {
            return new ErrorResponse(
                [__('user.errors.failed_update')],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new SuccessResponse(__('user.success_update'), [], Response::HTTP_OK);
    }

    public function activate(int $userId): UserResource|ErrorResponse {
        $foundUser = $this->userRepository->get($userId);
        if (!$foundUser) {
            return new ErrorResponse([__('user.errors.failed_find')], Response::HTTP_NOT_FOUND);
        }

        /**
         * @var User $authenticatedUser;
         */
        $authenticatedUser = Auth::user();

        $this->userValidationService->validatePreConditionsToActivate($foundUser, $authenticatedUser);
        if ($this->userValidationService->hasErrors()) {
            return new ErrorResponse(
                $this->userValidationService->getErrors(),
                $this->userValidationService->getStatus(),
            );
        }

        $activatedUser = $this->userRepository->activate($foundUser);
        return new UserResource($activatedUser, __('user.success_activate'));
    }

    public function deactivate(int $userId): UserResource|ErrorResponse {
        $requestedUser = $this->userRepository->get($userId);
        if (!$requestedUser) {
            return new ErrorResponse([__('user.errors.failed_find')], Response::HTTP_NOT_FOUND);
        }

        /**
         * @var User $authenticatedUser;
         */
        $authenticatedUser = Auth::user();

        $this->userValidationService->validatePreConditionsToDeactivate($requestedUser, $authenticatedUser);
        if ($this->userValidationService->hasErrors()) {
            return new ErrorResponse(
                $this->userValidationService->getErrors(),
                $this->userValidationService->getStatus(),
            );
        }

        $deactivatedUser = $this->userRepository->deactivate($requestedUser);
        return new UserResource($deactivatedUser, __('user.success_deactivate'));
    }
}
