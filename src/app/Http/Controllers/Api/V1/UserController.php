<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Resources\Api\V1\Responses\BadRequestErrorResponse;
use App\Resources\Api\V1\Responses\ErrorResponse;
use App\Resources\Api\V1\Responses\ExceptionErrorResponse;
use App\Resources\Api\V1\Responses\NotFoundErrorResponse;
use App\Resources\Api\V1\Responses\SuccessResponse;
use App\Resources\Api\V1\Responses\UnprocessableRquestErrorResponse;
use App\Resources\Api\V1\UserResource;
use App\Services\Users\UserValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                $request->get('limit', (int) config('settings.max_results_user')),
            ),
        );

        return new SuccessResponse('user.success.find.list', $users->toArray($request));
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
            return new NotFoundErrorResponse('user.failed.find.fetch');
        }

        $userData = new UserResource($user);
        return new SuccessResponse('user.successfind.fetch', $userData->toArray());
    }

    public function update(Request $request, int $userId): SuccessResponse|ErrorResponse {
        $input = $request->only(['bio', 'channel_name']);

        /**
         * @var User $authenticatedUser;
         */
        $authenticatedUser = Auth::user();

        try {
            $requestedUser = $this->userRepository->get($userId);
            if (!$requestedUser) {
                return new NotFoundErrorResponse('user.failed.find.fetch');
            }

            $this->userValidationService->validatePreConditionsToUpdate($requestedUser, $authenticatedUser);
            if ($this->userValidationService->hasErrors()) {
                return new ErrorResponse(
                    $this->userValidationService->getErrors(),
                    $this->userValidationService->getStatus(),
                );
            }

            $requestValidationErrors = $this->userValidationService->validateUpdateRequest($input);
            if ($requestValidationErrors) {
                return new UnprocessableRquestErrorResponse($requestValidationErrors);
            }

            $updatedUser = $this->userRepository->update($requestedUser, $input);
            if (!$updatedUser) {
                return new BadRequestErrorResponse('user.failed.update.unknown');
            }

            $userData = new UserResource($updatedUser);
            return new SuccessResponse('user.success.update.single', $userData->toArray());
        } catch (Throwable $exception) {
            Log::error('User update: unexpected error', [
                'userId' => $userId,
                'input' => $input,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('user.failed.update.unknown');
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
                return new NotFoundErrorResponse('user.failed.find.fetch');
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

            return new SuccessResponse('user.success.update.activate', $activatedUser->toArray());
        } catch (Throwable $exception) {
            Log::error('User activate: unexpected error', [
                'userId' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('user.failed.update.unknown');
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
                return new NotFoundErrorResponse('user.failed.find.fetch');
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

            return new SuccessResponse('user.success.update.deactivate', $deactivatedUser->toArray());
        } catch (Throwable $exception) {
            Log::error('User deactivate: unexpected error', [
                'userId' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return new ExceptionErrorResponse('user.failed.update.unknown');
        }
    }
}
