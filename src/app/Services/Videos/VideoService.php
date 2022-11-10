<?php

namespace App\Services\Videos;

use App\Models\Video;
use App\Services\FileService;
use FFMpeg\FFMpeg;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class VideoService
{
    public function __construct() {}

    public function generateFilename(): string {
        return sprintf("%s-%s", Str::random(8), date("Ymd"));
    }

    public function generateVkey(): string {
        return Str::random(14);
    }

    public function extractMeta(string $path): ?array {
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
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
        ];

        return $meta;
    }

    public function getProcessedFilesPaths(string $filename, string $directory): ?array {
        $basePath = FileService::getVideo($directory, $filename);

        return glob("$basePath*");
    }

    public function getThumbnailsPaths(string $filename, string $directory): ?array {
        $basePath = sprintf(
            "%s/%s/%s",
            FileService::getThumbnailsDirectory(),
            $directory,
            $filename,
        );

        return glob("$basePath*");
    }

    public function deleteMedia(Video $video): bool {
        $files = array_merge(
            $this->getProcessedFilesPaths($video->filename, $video->directory),
            $this->getThumbnailsPaths($video->filename, $video->directory),
        );

        return File::delete($files);
    }
}
