<?php

namespace App\Services\Videos\Processing;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Media\Video;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\Coordinate\Dimension;

abstract class MediaProcessingService
{
    protected FFMpeg $ffmpeg;
    protected FFProbe $ffprobe;
    protected Video $video;
    protected StreamCollection $streams;

    protected function init(string $path, array $dimensions): void {
        $this->ffmpeg = FFMpeg::create();
        $this->ffprobe = FFProbe::create();
        $this->video = $this->ffmpeg->open($path);
        $this->streams = $this->getStreams(($path));

        $this->video
            ->filters()
            ->resize(new Dimension(current($dimensions), end($dimensions)))
            ->synchronize();
    }

    protected abstract function process(string $path, string $filename, string $destinationDirectory, array $dimensions): array;

    protected function getStreams(string $path): ?StreamCollection {
        return $this->video?->getStreams($path);
    }
    protected function getAudioStreams(): ?Stream {
        return $this->streams->audios()?->first();
    }

    protected function getVideoStreams(): ?Stream {
        return $this->streams->videos()?->first();
    }

    protected function getVideoDuration(): ?float {
        return $this->streams->videos()?->first()?->get('duration');
    }
}
