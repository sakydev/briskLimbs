<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class ValidationService
{
    private array $errors;
    private string $status;

    public function getErrors(): array {
        return $this->errors ?? [];
    }

    public function hasErrors(): bool {
        return !empty($this->getErrors());
    }

    protected function addError(string $error): void {
        $this->errors[] = $error;
    }

    protected function resetErrors(): void {
        $this->errors = [];
    }

    public function getStatus(): string {
        return $this->status;
    }

    protected function setStatus(string $status): void {
        $this->status = $status;
    }

    protected function validateRules(array $input, array $rules): ?array {
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
