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
            $this->addError(__('video.errors.failed_upload_permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanUpdate(Video $video, User $user): bool {
        if ($user->isInactive() || $user->getAuthIdentifier() !== $video->user_id) {
            $this->addError(__('video.errors.failed_update_permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateAlreadyActive(Video $video): bool {
        if ($video->state === Video::VIDEO_STATE_ACTIVE) {
            $this->addError(__('video.errors.failed_already_activate'));
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return false;
        }

        return true;
    }

    public function validateAlreadyInactive(Video $video): bool {
        if ($video->state === VIDEO::VIDEO_STATE_INACTIVE) {
            $this->addError(__('video.errors.failed_already_deactivate'));
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

    public function validateUploadRequest(array $input): ?array {
        $iniMaxFilesize = convertToBytes(ini_get('upload_max_filesize'));
        $configMaxFilesize = config('settings.max_filesize_video');

        // always prefer ini if it is smaller to prevent unexpected errors
        $maxFilesize = $iniMaxFilesize < $configMaxFilesize ? $iniMaxFilesize : $configMaxFilesize;

        $rules = [
            'file' => [
                'required',
                'mimes:' . str_replace(
                    '.',
                    '',
                    config('settings.supported_formats_video')
                ),
                'max:' . $maxFilesize,

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
        ];

        return $this->validateRules($input, $rules);
    }
}
