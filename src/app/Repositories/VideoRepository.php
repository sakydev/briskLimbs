<?php

namespace App\Repositories;

use App\Models\Video;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Collection;

class VideoRepository
{
    public function get(int $videoId): ?Video
    {
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

        $input['state'] = Video::VIDEO_STATE_ACTIVE;
        $input['scope'] = Video::VIDEO_SCOPE_PUBLIC;
        $input['directory'] = FileService::getDatedDirectoryName();

        $input['converted_at'] = null;

        return (new Video())->create($input);
    }

    public function update(Video $video, array $fieldValueParis): bool
    {
        return $video->update($fieldValueParis);
    }

    public function updateById(int $videoId, array $fieldValuePairs): bool {
        return (new Video())->where('id', $videoId)->update($fieldValuePairs);
    }

    public function updateStatus(Video $video, string $status): bool
    {
        $fieldValueParis = ['status' => $status];
        return $this->update($video, $fieldValueParis);
    }

    public function updateState(Video $video, string $state): bool
    {
        $fieldValueParis = ['state' => $state];
        return $this->update($video, $fieldValueParis);
    }
}
