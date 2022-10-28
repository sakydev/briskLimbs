<?php

namespace App\Repositories;

use App\Models\Video;

class VideoRepository
{
    public function get(int $videoId): ?Video
    {
        return (new Video())->where('id', $videoId)->first();
    }

    public function create(array $input, string $vkey, string $filename, int $userId): Video
    {
        $input['user_id'] = $userId;
        $input['vkey'] = $vkey;
        $input['filename'] = $filename;

        return (new Video())->create($input);
    }

    public function update(array $fieldValueParis, int|Video $video): ?int
    {
        if ($video instanceof Video) {
            return $video->update($fieldValueParis);
        }

        return (new Video())->where('id', $video)->update($fieldValueParis);
    }

    public function updateStatus(string $status, int|Video $video): bool
    {
        $fieldValueParis = ['status' => $status];
        return $this->update($fieldValueParis, $video);
    }

    public function updateState(string $state, int|Video $video): bool
    {
        $fieldValueParis = ['state' => $state];
        return $this->update($fieldValueParis, $video);
    }
}
