<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CategoryValidationService extends ValidationService
{
    public function validateCanCreate(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('category.failed.store.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanUpdate(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('category.failed.update.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanDelete(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('category.failed.delete.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateAlreadyPublished(Category $category): bool {
        if ($category->isPublished()) {
            $this->addError(__('category.failed.update.already_published'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyUnpublished(Category $category): bool {
        if (!$category->isPublished()) {
            $this->addError(__('category.failed.update.already_unpublished'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validatePreConditionsToPublish(Category $category, User $user): void {
        $this->resetErrors();


        if (!$this->validateCanUpdate($user)) {
            return;
        }

        if (!$this->validateAlreadyPublished($category)) {
            return;
        }
    }

    public function validatePreConditionsToUnpublish(Category $category, User $user): void {
        $this->resetErrors();


        if (!$this->validateCanUpdate($user)) {
            return;
        }

        if (!$this->validateAlreadyUnpublished($category)) {
            return;
        }
    }

    public function validateCreateRequest(array $input): ?array {
        $rules = [
            'name' => ['required', 'string', 'min:4', 'max:100', 'unique:categories'],
            'description' => ['string', 'min:10', 'max:3000'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateUpdateRequest(array $input): ?array {
        $rules = [
            'name' => ['sometimes', 'required', 'string', 'min:4', 'max:100', 'unique:categories'],
            'description' => ['sometimes', 'required', 'string', 'min:10', 'max:3000'],
        ];

        return $this->validateRules($input, $rules);
    }
}
