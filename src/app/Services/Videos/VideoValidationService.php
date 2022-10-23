<?php

namespace App\Services\Videos;

use App\Models\User;
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

    public function validateCanUpdate(User $user): ?array
    {
        $errors = [];
        if (!$user->canUpload()) {
            $errors[] = __('video.errors.failed_upload_permissions');

            return $errors;
        }

        return null;
    }

    public function validateUploadRequest(array $input): ?array
    {
        $rules = [
            'file' => [
                'required',
                'mimes:' . str_replace(
                    '.',
                    '',
                    config('settings.supported_formats_video')
                ),
                'max:' . config('settings.max_filesize_video') * 1000,

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
