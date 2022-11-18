<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;

abstract class UploadService
{
    abstract public function store(UploadedFile $file, string $filename): ?string;
}
