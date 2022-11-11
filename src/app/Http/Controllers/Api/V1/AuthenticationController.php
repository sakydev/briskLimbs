<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\UserResource;
use App\Services\Users\UserValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationController extends Controller
{
    public function __construct(
        private UserValidationService $userValidationService,
        private UserRepository $userRepository,
    ) {}

    public function register(Request $request): UserResource|ErrorResponse {
        $input = $request->all();

        try {
            $this->userValidationService->validatePreConditionsToRegister();
            if ($this->userValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->userValidationService->getErrors(),
                    $this->userValidationService->getStatus(),
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

            return new UserResource($createdUser, __('user.success.store.single'), true);
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Register: unexpected error', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function login(Request $request): UserResource|ErrorResponse {
        $input = $request->only(['username', 'password']);

        try {
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
            return new UserResource($user, __('user.success.login'), true);
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Login: unexpected error', [
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
