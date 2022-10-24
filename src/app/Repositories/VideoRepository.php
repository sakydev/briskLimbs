<?php

namespace App\Repositories;

use App\Models\Video;

class VideoRepository
{
    public function getById(int $videoId): ?Video
    {
        return (new Video())->where('id', $videoId)->first();
    }

    public function create(array $input, string $vkey, string $filename, int $userId): Video
    {
        unset($input['file']);
        $input['vkey'] = $vkey;
        $input['filename'] = $filename;
        $input['user_id'] = $userId;

        return (new Video())->create($input);
    }

    public function updateById(array $input, int $videoId): ?int
    {
        return (new Video())->where('id', $videoId)->update($input);
    }
}
