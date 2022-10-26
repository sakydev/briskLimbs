<?php

namespace App\Jobs;

use App\Models\Video;
use App\Repositories\VideoRepository;
use App\Services\Videos\Processing\ThumbnailProcessingService;
use App\Services\Videos\Processing\VideoProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

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
        $videoRepository->updateStatus(Video::VIDEO_PROCESSING_PROGRESS, $this->video);

        $destinations = $this->makeDestinationDirectories();
        $thumbnailsDestination = $destinations['thumbnails'];
        $videosDestination = $destinations['videos'];

        $videos = [];
        $thumbnails = [];

        $path = $this->getFullInputPath();

        foreach ($videoProcessingService->getProcessableQualities() as $quality => $dimensions) {
            $videos[$quality] = $videoProcessingService->process(
                $path,
                $this->video->filename,
                $videosDestination,
                $dimensions,
            );
            $thumbnails[$quality] = $thumbnailProcessingService->process(
                $path,
                $this->video->filename,
                $thumbnailsDestination,
                $dimensions,
            );
        }

        $videoRepository->updateStatus(Video::VIDEO_PROCESSING_SUCCESS, $this->video);
    }

    private function makeDestinationDirectories(): array
    {
        $disk = Storage::disk('local');
        $videos = sprintf('%s/%s', config('paths.videos'), config('paths.dated'));
        $thumbnails = sprintf('%s/%s', config('paths.thumbnails'), config('paths.dated'));

        $disk->makeDirectory($videos);
        $disk->makeDirectory($thumbnails);

        return [
            'videos' => storage_path("app/{$videos}"),
            'thumbnails' => storage_path("app/{$thumbnails}"),
        ];
    }

    private function getFullInputPath(): string
    {
        return storage_path(
            sprintf(
                'app/%s/%s.mp4',
                config('paths.temporary_videos'),
                $this->video->filename
            )
        );
    }
}
