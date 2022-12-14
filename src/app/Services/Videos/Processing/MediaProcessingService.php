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
    protected string $path;
    protected array $meta;

    protected function init(string $path, array $meta): void {
        $this->ffmpeg = FFMpeg::create();
        $this->ffprobe = FFProbe::create();
        $this->video = $this->ffmpeg->open($path);
        $this->path = $path;
        $this->meta = $meta;

        $this->video
            ->filters()
            ->resize(new Dimension($this->getWidth(), $this->getHeight()))
            ->synchronize();
    }

    abstract protected function process(string $path, string $filename, string $destinationDirectory, array $meta): array;

    protected function getStreams(): ?StreamCollection {
        return $this->video?->getStreams($this->path);
    }

    protected function getVideoStreams(): ?Stream {
        return $this->getStreams()->videos()?->first();
    }

    protected function getVideoDuration(): ?float {
        return $this->meta['duration'];
    }

    protected function getWidth(): ?int {
        return $this->meta['width'];
    }

    protected function getHeight(): ?int {
        return $this->meta['height'];
    }
}
