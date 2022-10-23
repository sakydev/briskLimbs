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
            return $errors[] = [
                'title' => 'User must be active',
                'description' => 'User must be active in order to upload videos',
            ];
        }
    }

    public function validateRequest(array $input): ?array
    {
        $rules = [
            'file' => [
                'required',
                'mimes:' . env('support_video_formats'),
                'max:' . env('max_video_upload_size'),

            ],
            'title' => ['required', 'string', 'min:10', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
        ];

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
