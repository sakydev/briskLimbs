<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
use App\Services\Users\UserValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private UserValidationService $userValidationService,
        private UserRepository $userRepository,
    ) {}
    public function store(Request $request): UserResource|ErrorResponse {
        $input = $request->all();

        $accessValidationErrors = $this->userValidationService->validateCanRegister();
        if ($accessValidationErrors) {
            return new ErrorResponse(
                $accessValidationErrors,
                Response::HTTP_FORBIDDEN
            );
        }

        $requestValidationErrors = $this->userValidationService->validateRegisterRequest($input);
        if ($requestValidationErrors) {
            return new ErrorResponse(
                $requestValidationErrors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $createdUser = $this->userRepository->create($input);
        if (!$createdUser) {
            return new ErrorResponse(
                [__('auth.failed_insert')],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new UserResource($createdUser, __('user.success_registration'), true);
    }

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
        $accessValidationErrors = $this->userValidationService->validateCanUpdate(
            $userId,
            $user,
        );
        if ($accessValidationErrors) {
            return new ErrorResponse(
                $accessValidationErrors,
                Response::HTTP_FORBIDDEN
            );
        }

        $requestValidationErrors = $this->userValidationService->validateUpdateRequest($input);
        if ($requestValidationErrors) {
            return new ErrorResponse(
                $requestValidationErrors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $updatedUser = $this->userRepository->update($userId, $input);
        if (!$updatedUser) {
            return new ErrorResponse(
                [__('user.errors.failed_update')],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new SuccessResponse(__('user.success_update'), [], Response::HTTP_OK);
    }

    public function login(Request $request): UserResource|ErrorResponse {
        $input = $request->only(['username', 'password']);

        $loginRequestValidationErrors = $this->userValidationService->validateLoginRequest($input);
        if ($loginRequestValidationErrors) {
            return new ErrorResponse(
                $loginRequestValidationErrors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $loggedIn = Auth::attempt($input);
        if (!$loggedIn) {
            return new ErrorResponse(
                [__('auth.failed')],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user = Auth::user();
        return new UserResource($user, __('user.success_login'), true);
    }
}
