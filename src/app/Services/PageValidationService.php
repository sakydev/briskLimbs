<?php

namespace App\Services;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class PageValidationService extends ValidationService
{
    public function validateCanCreate(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('page.failed.update.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCreateRequest(array $input): ?array {
        $rules = [
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'content' => ['required', 'string', 'min:10', 'max:3000'],
            'slug' => ['required', 'string', 'min:5'],
        ];

        return $this->validateRules($input, $rules);
    }
}
