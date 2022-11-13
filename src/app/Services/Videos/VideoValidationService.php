<?php

namespace App\Services\Videos;

use App\Models\User;
use App\Models\Video;
use App\Services\ValidationService;
use Symfony\Component\HttpFoundation\Response;

class VideoValidationService extends ValidationService
{
    public function validateCanUpload(User $user): bool {
        if (!$user->canUpload()) {
            $this->addError(__('video.failed.store.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanUpdate(Video $video, User $user): bool {
        if (!$user->isAdmin() && ($user->isInactive() || $user->getAuthIdentifier() !== $video->user_id)) {
            $this->addError(__('video.failed.update.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateAlreadyActive(Video $video): bool {
        if ($video->state === Video::STATE_ACTIVE) {
            $this->addError(__('video.failed.update.already.active'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyInactive(Video $video): bool {
        if ($video->state === VIDEO::STATE_INACTIVE) {
            $this->addError(__('video.failed.update.already.inactive'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyPublic(Video $video): bool {
        if ($video->scope === VIDEO::SCOPE_PUBLIC) {
            $this->addError(__('video.failed.update.already.public'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyPrivate(Video $video): bool {
        if ($video->scope === VIDEO::SCOPE_PRIVATE) {
            $this->addError(__('video.failed.update.already.private'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyUnlisted(Video $video): bool {
        if ($video->scope === VIDEO::SCOPE_UNLISTED) {
            $this->addError(__('video.failed.update.already.unlisted'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validatePreConditionsToUpdate(Video $video, User $user): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($video, $user)) {
            return;
        }
    }

    public function validatePreConditionsToDelete(Video $video, User $user): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($video, $user)) {
            return;
        }
    }

    public function validatePreConditionsToActivate(Video $video, User $user): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($video, $user)) {
            return;
        }

        if (!$this->validateAlreadyActive($video)) {
            return;
        }
    }

    public function validatePreConditionsToDeactivate(Video $video, User $user): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($video, $user)) {
            return;
        }

        if (!$this->validateAlreadyInactive($video)) {
            return;
        }
    }

    public function validatePreConditionsToMakePublic(Video $video, User $user): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($video, $user)) {
            return;
        }

        if (!$this->validateAlreadyPublic($video)) {
            return;
        }
    }

    public function validatePreConditionsToMakePrivate(Video $video, User $user): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($video, $user)) {
            return;
        }

        if (!$this->validateAlreadyPrivate($video)) {
            return;
        }
    }

    public function validatePreConditionsToMakeUnlisted(Video $video, User $user): void {
        $this->resetErrors();

        if (!$this->validateCanUpdate($video, $user)) {
            return;
        }

        if (!$this->validateAlreadyUnlisted($video)) {
            return;
        }
    }

    public function validateUploadRequest(array $input): ?array {
        $rules = [
            'file' => [
                'required',
                'mimes:' . str_replace(
                    '.',
                    '',
                    config('settings.supported_formats_video')
                ),
                'max:' . getMaxUploadSizeInKB(),

            ],
            'title' => ['required', 'string', 'min:10', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateUpdateRequest(array $input): ?array {
        $rules = [
            'title' => ['sometimes', 'required', 'string', 'min:10', 'max:100'],
            'description' => ['sometimes', 'required', 'string', 'min:10', 'max:3000'],
            'scope' => ['sometimes', 'required', 'string', 'in:public,private,unlisted'],
            'state' => ['sometimes', 'required', 'string', 'in:active,inactive'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateSearchRequest(array $input): ?array {
        $rules = [
            'query' => ['required', 'string', 'min:3', 'max:50'],
        ];

        return $this->validateRules($input, $rules);
    }
}
