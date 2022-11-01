<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserValidationService
{
    private array $errors;
    private string $status;

    public function validateCanRegister(): ?array {
        if (!config('settings.allow_registrations')) {
            return [[
               'title' => '',
               'description' => __('user.errors.failed_registration_restriction'),
            ]];
        }

        return null;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function hasErrors(): bool {
        return !empty($this->getErrors());
    }

    private function addError(string $error): void {
        $this->errors[] = $error;
    }

    private function resetErrors(): void {
        $this->errors = [];
    }

    public function getStatus(): string {
        return $this->status;
    }

    private function setStatus(string $status): void {
        $this->status = $status;
    }

    public function validateCanUpdate(int $inputUserId, User $authUser): bool {
        if (
            !$authUser->isAdmin()
            && $inputUserId != $authUser->getAuthIdentifier()
        ) {
            $this->addError(__('user.errors.failed_update_permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateAlreadyActive(User $user): bool {
        if ($user->isActive()) {
            $this->addError(__('user.errors.failed_activate'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyInactive(User $user): bool {
        if ($user->isInactive()) {
            $this->addError(__('user.errors.failed_deactivate'));
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

    public function validatePreConditionsToRegister(): void {
        $this->resetErrors();

        if (!$this->validateCanRegister()) {
            return;
        }
    }

    public function validatePreConditionsToUpdate(int $userId, User $authUser): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($userId, $authUser)) {
            return;
        }
    }

    public function validatePreConditionsToActivate(User $inputUser, User $authUser): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($inputUser->id, $authUser)) {
            return;
        }

        if (!$this->validateAlreadyActive($inputUser)) {
            return;
        }
    }

    public function validatePreConditionsToDeactivate(User $inputUser, User $authUser): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($inputUser->id, $authUser)) {
            return;
        }

        if (!$this->validateAlreadyInactive($inputUser)) {
            return;
        }
    }

    private function validateRules(array $input, array $rules): ?array {
        $validator = Validator::make($input, $rules);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->messages()->get('*') as $title => $description) {
                $errors[] = [
                    'title' => $title,
                    'description' => current($description),
                ];
            }

            return $errors;
        }

        return null;
    }
}
