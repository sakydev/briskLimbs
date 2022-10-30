<?php

namespace App\Services\Videos\Processing;
use FFMpeg\Coordinate\TimeCode;

class ThumbnailProcessingService extends MediaProcessingService
{
    public function process(string $path, string $filename, string $destinationDirectory, array $meta): array
    {
        $this->init($path, $meta);

        $duration = $this->getVideoDuration();
        $framerateInterval = $this->calculateThumbnailFramerateInterval($duration);

        $generated = [];
        for($iteration = 0; $iteration < config('settings.max_thumbnails_count'); $iteration++) {
            $prefix = sprintf('%s-%s-%d', $filename, $this->getHeight(), $iteration);
            $thumbnailFilePath = $this->generateOutputPath($prefix, $destinationDirectory);
            $generated[] = $this->generateThumbnail(
                $iteration,
                $framerateInterval,
                $thumbnailFilePath
            );
        }

        return $generated;

    }

    protected function generateOutputPath(string $prefix, string $destinationDirectory): string
    {
        return sprintf(
            "%s/%s.%s",
            $destinationDirectory,
            $prefix,
            config('settings.default_thumbnail_extension'),
        );
    }

    private function generateThumbnail(int $iteration, int $framerateInterval, string $thumbnailFilePath): string
    {
        $nextTimestamp = $this->calculateNextTimestamp($iteration, $framerateInterval);
        $frame = $this->video->frame(TimeCode::fromSeconds($nextTimestamp));

        $frame->save($thumbnailFilePath);

        return basename($thumbnailFilePath);
    }

    private function calculateNextTimestamp(int $iteration, int $framerateInterval): int
    {
        $timestamp = ($iteration * $framerateInterval) + $framerateInterval;
        if ($iteration + 1 == config('settings.max_thumbnails_count')) {
            $timestamp  = $timestamp - 1;
        }

        return $timestamp;
    }

    private function calculateThumbnailFramerateInterval(int $duration): int
    {
        $options = [60, 30, 10, 5, 2, 1];
        foreach ($options as $interval) {
            $dividable = ($interval * config('settings.max_thumbnails_count')) <= $duration;
            if ($dividable) {
                return $interval;
            }
        }

        return last($options);
    }
}
