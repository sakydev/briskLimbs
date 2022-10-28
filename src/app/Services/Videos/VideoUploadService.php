<?php

namespace App\Services\Videos;

use App\Services\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoUploadService extends UploadService
{
    public function store(UploadedFile $file, string $filename): ?string
    {
        $this->createTemporaryDirectories();
        $extension = $file->extension() ?? 'mp4';
        $path = Storage::disk('local')->putFileAs(
            config('paths.temporary_videos'),
            $file,
            $filename . '.' . $extension
        );

        return $path ? storage_path('app/' . $path) : null;
    }
}
