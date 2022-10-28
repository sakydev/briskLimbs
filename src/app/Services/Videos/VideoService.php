<?php

namespace App\Services\Videos;

use FFMpeg\FFMpeg;
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

    public function extractMeta(string $path): array {
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($path);
        $streams = $video->getStreams()?->videos()?->first();

        $meta = [
            'codec' => $streams->get('codec_name'),
            'width' => $streams->get('width'),
            'height' => $streams->get('height'),
            'aspect' => $streams->get('display_aspect_ratio'),
            'duration' => $streams->get('duration'),
            'bitrate' => $streams->get('bitrate'),
        ];


        return $meta;
    }
}
