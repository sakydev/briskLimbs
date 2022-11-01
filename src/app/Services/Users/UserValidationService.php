<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserValidationService
{
    public function validateCanRegister(): ?array {
        if (!config('settings.allow_registrations')) {
            return [[
               'title' => '',
               'description' => __('user.errors.failed_registration_restriction'),
            ]];
        }

        return null;
    }

    public function validateCanUpdate(int $updateUserId, User $authenticatedUser): ?array {
        if (
            !$authenticatedUser->isAdmin()
            && $updateUserId != $authenticatedUser->getAuthIdentifier()
        ) {
            return [[
                'title' => '',
                'description' => __('user.errors.failed_update_permissions'),
            ]];
        }

        return null;
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
