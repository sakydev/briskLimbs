<?php declare(strict_types=1);

namespace App\Repositories;

use App\Models\Video;
use App\Services\FileService;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoRepository
{
    public function get(int $videoId): ?Video {
        return Video::where('id', $videoId)->first();
    }

    public function list(array $parameters, int $page, int $limit): LengthAwarePaginator {
        $videos = new Video();
        foreach ($parameters as $name => $value) {
            $videos = $videos::where($name, $value);
        }

        return $videos->orderBy('id', 'DESC')->paginate($limit, '*', $page, $page);
    }

    public function search(string $query, int $page, int $limit): LengthAwarePaginator {
        return Video::search($query)
            ->whereIn('state', [Video::STATE_ACTIVE])
            ->whereIn('status', [Video::PROCESSING_SUCCESS])
            ->whereIn('scope', [Video::SCOPE_PUBLIC])
            ->paginate($limit, $page, $page);
    }

    public function create(
        array $input,
        string $filename,
        string $vkey,
        array $meta,
        int $userId,
    ): Video {
        $input['user_id'] = $userId;
        $input['category_id'] = $input['category_id'] ?? 1;
        $input['vkey'] = $vkey;
        $input['filename'] = $filename;
        $input['original_meta'] = $meta;
        $input['duration'] = $meta['duration'];

        $input['scope'] = $input['scope'] ?? config('settings.default_video_scope');
        $input['state'] = $input['state'] ?? config('settings.default_video_state');

        // set the user configurable fields
        $input['allow_comments'] = config('settings.allow_comments');
        $input['default_thumbnail'] = config('settings.default_thumbnail');
        $input['allow_embed'] = config('settings.allow_embeds');
        $input['allow_download'] = config('settings.allow_downloads');

        // set defaults
        $input['status'] = Video::PROCESSING_PENDING;
        $input['directory'] = FileService::getDatedDirectoryName();

        $input['allow_comments'] = $input['allow_comments'] ?? config('settings.allow_comments');

        $input['converted_at'] = null;

        return Video::create($input);
    }

    public function update(Video $video, array $fieldValuePairs): Video {
        foreach ($fieldValuePairs as $field => $value) {
            $video->$field = $value;
        }

        $video->save();

        return $video;
    }

    public function delete(Video $video): bool {
        return $video->delete();
    }

    public function updateStatus(Video $video, string $status): Video {
        $video->status = $status;
        $video->save();

        return $video;
    }

    public function activate(Video $video): Video {
        $video->state = Video::STATE_ACTIVE;
        $video->save();

        return $video;
    }

    public function deactivate(Video $video): Video {
        $video->state = Video::STATE_INACTIVE;
        $video->save();

        return $video;
    }

    public function makePublic(Video $video): Video {
        $video->scope = Video::SCOPE_PUBLIC;
        $video->save();

        return $video;
    }

    public function makePrivate(Video $video): Video {
        $video->scope = Video::SCOPE_PRIVATE;
        $video->save();

        return $video;
    }

    public function makeUnlisted(Video $video): Video {
        $video->scope = Video::SCOPE_UNLISTED;
        $video->save();

        return $video;
    }
}
