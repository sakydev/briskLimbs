<?php

namespace App\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request = []): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'video_id' => $this->video_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
