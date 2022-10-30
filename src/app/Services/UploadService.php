<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class UploadService
{
    public abstract function store(UploadedFile $file, string $filename): ?string;
}
