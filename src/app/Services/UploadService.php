<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

abstract class UploadService
{
    public abstract function store(UploadedFile $file, string $filename): ?string;
}
