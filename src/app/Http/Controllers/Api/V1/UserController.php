<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Resources\Api\V1\ErrorResponse;
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

    public function show(int $userId): UserResource|ErrorResponse {
        $user = $this->userRepository->get($userId);
        if (!$user) {
            return new ErrorResponse([__('user.errors.failed_find')], Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user, __('user.success_find'));
    }

    public function store(Request $request): UserResource|ErrorResponse {
        $input = $request->all();

        $registrationAccessValidationErrors = $this->userValidationService->validateCanRegister();
        if ($registrationAccessValidationErrors) {
            return new ErrorResponse(
                $registrationAccessValidationErrors,
                Response::HTTP_FORBIDDEN
            );
        }

        $requestValidationErrors = $this->userValidationService->validateRegisterInput($input);
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
