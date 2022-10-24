<?php

namespace App\Services\Videos;

use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Validator;

class VideoValidationService
{
    public function validateCanUpload(User $user): ?array
    {
        $errors = [];
        if (!$user->canUpload()) {
            $errors[] = __('video.errors.failed_upload_permissions');

            return $errors;
        }

        return null;
    }

    public function validateCanUpdate(User $user, Video $video): ?array
    {
        $errors = [];
        if ($user->getAuthIdentifier() !== $video->user_id) {
            $errors[] = __('video.errors.failed_upload_permissions');

            return $errors;
        }

        return null;
    }

    public function validateUploadRequest(array $input): ?array
    {
        $iniMaxFilesize = convertToBytes(ini_get('upload_max_filesize'));
        $configMaxFilesize = config('settings.max_filesize_video');
        $maxFilesize = min($iniMaxFilesize, $configMaxFilesize);

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

        $validator = Validator::make($input, $rules);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->messages()->get('*') as $title => $description) {
                $errors[] = sprintf("%s: %s", $title, current($description));
            }

            return $errors;
        }

        return null;
    }

    public function validateUpdateRequest(array $input): ?array
    {
        $rules = [
            'title' => ['sometimes', 'required', 'string', 'min:10', 'max:100'],
            'description' => ['sometimes', 'required', 'string', 'min:10', 'max:3000'],
            'scope' => ['sometimes', 'required', 'string', 'in:public,private,unlisted'],
        ];

        $validator = Validator::make($input, $rules);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->messages()->get('*') as $title => $description) {
                $errors[] = sprintf("%s: %s", $title, current($description));
            }

            return $errors;
        }

        return null;
    }
}
