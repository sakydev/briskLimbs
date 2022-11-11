<?php

namespace App\Services;

use App\Models\Page;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class PageValidationService extends ValidationService
{
    public function validateCanCreate(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('page.failed.store.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanUpdate(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('page.failed.update.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanDelete(User $user): bool {
        if (!$user->isAdmin()) {
            $this->addError(__('page.failed.delete.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateAlreadyPublished(Page $page): bool {
        if ($page->isPublished()) {
            $this->addError(__('page.failed.update.already.published'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyUnpublished(Page $page): bool {
        if (!$page->isPublished()) {
            $this->addError(__('page.failed.update.already.unpublished'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validatePreConditionsToPublish(Page $page, User $user): void {
        $this->resetErrors();


        if (!$this->validateCanUpdate($user)) {
            return;
        }

        if (!$this->validateAlreadyPublished($page)) {
            return;
        }
    }

    public function validatePreConditionsToUnpublish(Page $page, User $user): void {
        $this->resetErrors();


        if (!$this->validateCanUpdate($user)) {
            return;
        }

        if (!$this->validateAlreadyUnpublished($page)) {
            return;
        }
    }

    public function validateCreateRequest(array $input): ?array {
        $rules = [
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'content' => ['required', 'string', 'min:10', 'max:3000'],
            'slug' => ['required', 'string', 'min:5'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateUpdateRequest(array $input): ?array {
        $rules = [
            'title' => ['sometimes', 'required', 'string', 'min:5', 'max:100'],
            'content' => ['sometimes', 'required', 'string', 'min:10', 'max:3000'],
            'slug' => ['sometimes', 'required', 'string', 'min:5'],
        ];

        return $this->validateRules($input, $rules);
    }
}
