<?php

namespace App\Jobs;

use App\Models\Video;
use App\Repositories\VideoRepository;
use App\Services\FileService;
use App\Services\Videos\Processing\ThumbnailProcessingService;
use App\Services\Videos\Processing\VideoProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class VideoProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Video $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ThumbnailProcessingService $thumbnailProcessingService,
        VideoProcessingService $videoProcessingService,
        VideoRepository $videoRepository,
    ): void {
        try {
            $videoRepository->updateStatus(Video::VIDEO_PROCESSING_PROGRESS, $this->video);

            $destinations = FileService::createMediaDestinationDirecctories();
            $thumbnailsDestination = $destinations['thumbnails'];
            $videosDestination = $destinations['videos'];

            $videos = [];
            $thumbnails = [];

            $completeFilename = sprintf("%s.%s",
                $this->video->filename,
                $this->video->original_meta['extension']
            );

            $path = FileService::getTemporaryVideo($completeFilename);
            $processableQualities = $videoProcessingService->getProcessableQualities(
                $this->video->getOriginalWidth(),
                $this->video->getOriginalHeight(),
            );

            foreach (array_keys($processableQualities) as $quality) {
                $thumbnails[$quality] = $thumbnailProcessingService->process(
                    $path,
                    $this->video->filename,
                    $thumbnailsDestination,
                    $this->video->original_meta,
                );
                $videos[$quality] = $videoProcessingService->process(
                    $path,
                    $this->video->filename,
                    $videosDestination,
                    $this->video->original_meta,
                );
            }

            $videoRepository->updateStatus(Video::VIDEO_PROCESSING_SUCCESS, $this->video);
        } catch (Throwable $exception) {
            report($exception);
            Log::error('Error processing video: ' . $exception->getMessage());
        }
    }
}
