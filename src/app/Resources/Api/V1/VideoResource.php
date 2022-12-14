<?php declare(strict_types=1);

namespace App\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request = []): array
    {
        return [
            'id' => $this->id,
            'vkey' => $this->vkey,
            'filename' => $this->filename,
            'title' => $this->title,
            'description' => $this->description,
            'state' => $this->state,
            'status' => $this->status,
            'duration' => $this->duration,
            'directory' => $this->directory,
            'default_thumbnail' => $this->default_thumbnail,
            'qualities' => $this->qualities,
            // 'tags' => $this->tags,
            'total_views' => $this->total_views,
            'total_comments' => $this->total_comments,
            'allow_comments' => $this->allow_comments,
            'allow_embed' => $this->allow_embed,
            'allow_download' => $this->allow_download,
            // 'server_url' => $this->server_url,
            'original_meta' => $this->original_meta,
            'converted_at' => $this->converted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
