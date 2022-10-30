<?php

namespace App\Services\Videos;

use App\Services\FileService;
use App\Services\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoUploadService extends UploadService
{
    public function store(UploadedFile $file, string $filename): ?string
    {
        $extension = $file->extension() ?? config('settings.default_video_extension');
        $completeFilename = "{$filename}.{$extension}";

        $path = Storage::disk('temporaryVideos')->putFileAs('', $file, $completeFilename);
        return $path ? FileService::getTemporaryVideo($completeFilename) : null;
    }
}
