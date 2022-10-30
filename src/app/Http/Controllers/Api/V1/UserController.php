<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
use App\Services\Users\UserValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(
        private UserValidationService $userValidationService,
        private UserRepository $userRepository,
    ) {}
    public function index(): JsonResponse {

    }

    public function store(Request $request): JsonResponse {
        $input = $request->all();

        $registrationAccessValidationErrors = $this->userValidationService->validateCanRegister();
        if ($registrationAccessValidationErrors) {
            return new ErrorResponse($registrationAccessValidationErrors, Response::HTTP_FORBIDDEN);
        }

        $requestValidationErrors = $this->userValidationService->validateRegisterInput($input);
        if ($requestValidationErrors) {
            return new ErrorResponse($requestValidationErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $createdUser = $this->userRepository->create($input);
        if (!$createdUser) {
            return new ErrorResponse([__('auth.failed_insert')], Response::HTTP_BAD_REQUEST);
        }

        $userDetails = [
            'username' => $createdUser['username'],
            'email' => $createdUser['email'],
            '_token' => $createdUser->createToken('auth_token')->plainTextToken,
        ];

        return new SuccessResponse(__('user.registration_success'), $userDetails, Response::HTTP_OK);
    }

    public function login(Request $request): JsonResponse {

    }
}
