<?php

namespace App\Services\Videos\Processing;

use FFMpeg\Format\Video;

class VideoProcessingService extends MediaProcessingService
{
    public function process(string $path, string $filename, string $destinationDirectory, array $dimensions): array
    {
        $this->init($path, $dimensions);

        $generated = [];
        $processableFormats = $this->getProcessableFormats();
        foreach ($processableFormats as $formatName => $format) {
            $prefix = sprintf('%s-%s.%s', $filename, implode('x', $dimensions), $formatName);
            $videoFilePath = $this->generateOutputPath($prefix, $destinationDirectory);
            $this->video->save($format, $videoFilePath);

            $generated[] = basename($prefix);
        }

        return $generated;
    }

    protected function generateOutputPath(string $prefix, string $destinationDirectory): string
    {
        return sprintf(
            "%s/%s",
            $destinationDirectory,
            $prefix,
        );
    }

    public function getSupportedFormats(): array
    {
        return [
            'x264' => new Video\X264(),
            'wmv' => new Video\WMV(),
            'webm' => new Video\WebM(),
        ];
    }

    public function getAllowedFormats(): array
    {
        return ['wmv', 'webm'];
    }

    public function getProcessableFormats(): array
    {
        $allowedFormats = $this->getAllowedFormats();
        $supportedFormats = $this->getSupportedFormats();
        $processableFormats = [];

        foreach ($allowedFormats as $format) {
            if (isset($supportedFormats[$format])) {
                $processableFormats[$format] = $supportedFormats[$format];
            }
        }

        return $processableFormats;
    }

    public function getSupportedQualities(): array
    {
        return [
            360 => [640, 360],
            720 => [1280, 720],
            1080 => [1920, 1080],
        ];
    }

    public function getAllowedQualities(): array
    {
        return [360, 720];
    }

    public function getProcessableQualities(): array
    {
        $allowedQualities = $this->getAllowedQualities();
        $supportedQualities = $this->getSupportedQualities();
        $processableQualities = [];

        foreach ($allowedQualities as $quality) {
            if (isset($supportedQualities[$quality])) {
                $processableQualities[$quality] = $supportedQualities[$quality];
            }
        }

        return $processableQualities;
    }
}
