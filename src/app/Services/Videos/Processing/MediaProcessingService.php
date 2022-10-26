<?php

namespace App\Services\Videos\Processing;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Media\Video;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\Coordinate\Dimension;

class MediaProcessingService
{
    protected FFMpeg $ffmpeg;
    protected FFProbe $ffprobe;
    protected Video $video;
    protected StreamCollection $streams;
    protected string $dimensions;

    public function __construct(
        private ThumbnailProcessingService $thumbnailProcessingService,
        private VideoProcessingService $videoProcessingService,
    ) {}

    private function init(string $path): void {
        $this->ffmpeg = FFMpeg::create();
        $this->ffprobe = FFProbe::create();
        $this->video = $this->ffmpeg->open($path);
        $this->streams = $this->getStreams(($path));
    }

    private function setDimensions(array $dimensions) {
        $this->dimensions = implode('x', $dimensions);
    }

    public function getDimensions() {
        return $this->dimensions;
    }

    public function process(string $path, string $filename, string $destinationDirectory): array {
        $this->init($path);

        $videos = [];
        $thumbnails = [];

        foreach ($this->videoProcessingService->getProcessableQualities() as $quality => $dimensions) {
            $this->video
                ->filters()
                ->resize(new Dimension(current($dimensions), end($dimensions)))
                ->synchronize();
            $this->setDimensions($dimensions);

            $videos[$quality] = $this->videoProcessingService->process($path, $filename, $destinationDirectory);
            $thumbnails[$quality] = $this->thumbnailProcessingService->process($path, $filename, $destinationDirectory);
        }

        return [
            'videos' => $videos,
            'thumbnails' => $thumbnails,
        ];
    }

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
