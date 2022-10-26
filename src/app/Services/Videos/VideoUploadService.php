<?php

namespace App\Services\Videos;

use App\Services\FileService;
use Illuminate\Http\UploadedFile;

class VideoUploadService extends FileService
{
    public function store(UploadedFile $file, string $filename): string
    {
        $this->createTemporaryDirectories();
        $extension = $file->extension() ?? 'mp4';
        return $this->getDisk()->putFileAs('temporary/videos', $file, $filename . '.' . $extension);
    }
}
