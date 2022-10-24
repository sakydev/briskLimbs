<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class FileService
{
    private const TEMPORARY_DIRECTORY = 'temporary';
    private const VIDEOS_DIRECTORY = 'videos';

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
        return $this->makeDirectory(self::TEMPORARY_DIRECTORY . '/' . self::VIDEOS_DIRECTORY);
    }

    public function makeDirectory(string $directory): bool
    {
        return $this->getDisk()->makeDirectory($directory);
    }
}
