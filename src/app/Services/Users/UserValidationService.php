<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\ValidationService;
use Symfony\Component\HttpFoundation\Response;

class UserValidationService extends ValidationService
{
    public function validateCanRegister(): bool {
        if (!config('settings.allow_registrations')) {
            $this->addError(__('user.failed.store.restricted'));
            $this->setStatus(Response::HTTP_FORBIDDEN);
        }

        return true;
    }

    public function validateCanUpdate(int $inputUserId, User $authUser): bool {
        if (
            !$authUser->isAdmin()
            && $inputUserId != $authUser->getAuthIdentifier()
        ) {
            $this->addError(__('user.failed.update.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateAlreadyActive(User $user): bool {
        if ($user->isActive()) {
            $this->addError(__('user.failed.update.already.active'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyInactive(User $user): bool {
        if ($user->isInactive()) {
            $this->addError(__('user.failed.update.already.inactive'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateRegisterRequest(array $input): ?array {
        $rules = [
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateLoginRequest(array $input): ?array {
        $rules = [
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'password' => ['required', 'string', 'min:4'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateUpdateRequest(array $input): ?array {
        $rules = [
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive'],
            'level' => ['sometimes', 'required', 'int', 'in:1,2,3,4,5'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateCanSeeUsers(User $user): bool {
        if ($user->isInactive()) {
            $this->addError(__('user.failed.find.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanUpdateSettings(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('user.failed.update.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validatePreConditionsToRegister(): void {
        $this->resetErrors();

        if (!$this->validateCanRegister()) {
            return;
        }
    }

    public function validatePreConditionsToUpdate(int $userId, User $authUser): void {
        $this->resetErrors();

        if (!$this->validateCanSeeUsers($authUser)) {
            return;
        }

        if (!$this->validateCanUpdate($userId, $authUser)) {
            return;
        }
    }

    public function validatePreConditionsToActivate(User $inputUser, User $authUser): void {
        $this->resetErrors();

        if (!$this->validateCanSeeUsers($authUser)) {
            return;
        }

        if (!$this->validateCanUpdate($inputUser->id, $authUser)) {
            return;
        }

        if (!$this->validateAlreadyActive($inputUser)) {
            return;
        }
    }

    public function validatePreConditionsToDeactivate(User $inputUser, User $authUser): void {
        $this->resetErrors();

        if (!$this->validateCanSeeUsers($authUser)) {
            return;
        }

        if (!$this->validateCanUpdate($inputUser->id, $authUser)) {
            return;
        }

        if (!$this->validateAlreadyInactive($inputUser)) {
            return;
        }
    }
}
