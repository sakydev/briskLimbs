<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class FileService
{
    public abstract function store(UploadedFile $file, string $filename): string;

    public function getDisk(): Filesystem
    {
        return Storage::build([
            'driver' => 'local',
            'root' => base_path() . '/storage/app',
        ]);
    }
    public function createTemporaryDirectories(): bool
    {
        return $this->makeDirectory('temporary/videos')
        && $this->makeDirectory('temporary/thumbnails')
        && $this->makeDirectory('temporary/avatars')
        && $this->makeDirectory('temporary/covers');
    }

    public function makeDirectory(string $directory): bool
    {
        return $this->getDisk()->makeDirectory($directory);
    }
}
