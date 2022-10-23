<?php

namespace App\Services\Videos;

use Illuminate\Support\Str;

class VideoService
{
    public function __construct() {}

    public function generateFilename(): string
    {
        return sprintf("%s-%s", Str::random(8), date("Ymd"));
    }

    public function generateVkey(): string
    {
        return Str::random(14);
    }
}
