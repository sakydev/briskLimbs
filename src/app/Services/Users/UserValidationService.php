<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Validator;

class UserValidationService
{
    public function validateCanRegister(): ?array {
        if (!config('settings.allow_registrations')) {
            return [[
               'title' => '',
               'description' => __('user.errors.restricted_registrations'),
            ]];
        }

        return null;
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

    public function validateRegisterInput(array $input): ?array {
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
}
