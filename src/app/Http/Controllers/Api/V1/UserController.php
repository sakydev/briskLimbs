<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Resources\Api\V1\ErrorResponse;
use App\Resources\Api\V1\SuccessResponse;
use App\Services\Users\UserValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserController extends Controller
{
    public function __construct(
        private UserValidationService $userValidationService,
        private UserRepository $userRepository,
    ) {}

    public function index(Request $request): SuccessResponse|ErrorResponse {
        $parameters = $request->only(['status', 'level']);

        /**
         * @var User $user;
         */
        $user = Auth::user();

        $this->userValidationService->validateCanSeeUsers($user);
        if ($this->userValidationService->hasErrors()) {
            return new ErrorResponse(
                $this->userValidationService->getErrors(),
                $this->userValidationService->getStatus(),
            );
        }

        $users = UserResource::collection(
            $this->userRepository->list(
                $parameters,
                $request->get('page', 1),
                $request->get('limit', 10),
            ),
        );

        return new SuccessResponse(
            __('user.success_list'),
            $users->toArray($request),
            Response::HTTP_OK,
        );
    }

    public function show(int $userId): SuccessResponse|ErrorResponse {
        /**
         * @var User $authenticatedUser;
         */
        $authenticatedUser = Auth::user();

        $this->userValidationService->validateCanSeeUsers($authenticatedUser);
        if ($this->userValidationService->hasErrors()) {
            return new ErrorResponse(
                $this->userValidationService->getErrors(),
                $this->userValidationService->getStatus(),
            );
        }

        $user = $this->userRepository->get($userId);
        if (!$user) {
            return new ErrorResponse(
                [__('user.errors.failed_find')],
                Response::HTTP_NOT_FOUND
            );
        }

        $userResource = new UserResource($user);
        return new SuccessResponse(
            __('user.success_find'),
            $userResource->toArray(),
            Response::HTTP_OK,
        );
    }

    public function update(Request $request, int $userId): SuccessResponse|ErrorResponse {
        $input = $request->except(['id', 'username', 'email', '_method']);

        /**
         * @var User $user;
         */
        $user = Auth::user();

        try {
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
        } catch (Throwable $exception) {
            report($exception);

            Log::error('User update: unexpected error', [
                'userId' => $userId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function activate(int $userId): SuccessResponse|ErrorResponse {
        /**
         * @var User $authenticatedUser;
         */
        $authenticatedUser = Auth::user();

        try {
            $requestedUser = $this->userRepository->get($userId);
            if (!$requestedUser) {
                return new ErrorResponse(
                    [__('user.errors.failed_find')],
                    Response::HTTP_NOT_FOUND
                );
            }


            $this->userValidationService->validatePreConditionsToActivate(
                $requestedUser,
                $authenticatedUser
            );
            if ($this->userValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->userValidationService->getErrors(),
                    $this->userValidationService->getStatus(),
                );
            }

            $activatedUser = $this->userRepository->activate($requestedUser);

            return new SuccessResponse(
                __('user.success_activate'),
                $activatedUser->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('User activate: unexpected error', [
                'userId' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function deactivate(int $userId): SuccessResponse|ErrorResponse {
        /**
         * @var User $authenticatedUser;
         */
        $authenticatedUser = Auth::user();

        try {
            $requestedUser = $this->userRepository->get($userId);
            if (!$requestedUser) {
                return new ErrorResponse(
                    [__('user.errors.failed_find')],
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->userValidationService->validatePreConditionsToDeactivate(
                $requestedUser,
                $authenticatedUser
            );
            if ($this->userValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->userValidationService->getErrors(),
                    $this->userValidationService->getStatus(),
                );
            }

            $deactivatedUser = $this->userRepository->deactivate($requestedUser);

            return new SuccessResponse(
                __('user.success_deactivate'),
                $deactivatedUser->toArray(),
                Response::HTTP_OK,
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error('User deactivate: unexpected error', [
                'userId' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return new ErrorResponse(
                [__('general.errors.unknown')],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
