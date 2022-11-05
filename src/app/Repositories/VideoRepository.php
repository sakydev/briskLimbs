<?php

namespace App\Repositories;

use App\Models\Video;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Collection;

class VideoRepository
{
    public function get(int $videoId): ?Video {
        return (new Video())->where('id', $videoId)->first();
    }

    public function list(array $parameters, int $page, int $limit): Collection {
        $skip = ($page * $limit) - $limit;

        $users = new Video();
        foreach ($parameters as $name => $value) {
            $users = $users->where($name, $value);
        }

        return $users->skip($skip)->take($limit)->orderBy('id', 'DESC')->get();
    }

    public function create(
        array $input,
        string $filename,
        string $vkey,
        array $meta,
        int $userId,
    ): Video {
        $input['user_id'] = $userId;
        $input['vkey'] = $vkey;
        $input['filename'] = $filename;
        $input['original_meta'] = $meta;

        $input['status'] = Video::PROCESSING_PENDING;
        $input['state'] = Video::STATE_ACTIVE;
        $input['scope'] = Video::SCOPE_PUBLIC;
        $input['directory'] = FileService::getDatedDirectoryName();

        $input['converted_at'] = null;

        return (new Video())->create($input);
    }

    public function update(Video $video, array $fieldValueParis): bool {
        return $video->update($fieldValueParis);
    }

    public function updateById(int $videoId, array $fieldValuePairs): bool {
        return (new Video())->where('id', $videoId)->update($fieldValuePairs);
    }

    public function updateStatus(Video $video, string $status): Video {
        $video->status = $status;
        $video->save();

        return $video;
    }

    public function activate(Video $video): Video {
        $video->state = VIDEO::STATE_ACTIVE;
        $video->save();

        return $video;
    }

    public function deactivate(Video $video): Video {
        $video->state = VIDEO::STATE_INACTIVE;
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
