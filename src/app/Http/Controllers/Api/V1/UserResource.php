<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    private bool $includeToken;
    private string $message;

    public function __construct($resource, string $message, bool $includeToken = false)
    {
        parent::__construct($resource);

        $this->message = $message;
        $this->includeToken = $includeToken;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->includeToken) {
            $data['_token'] = $this->createToken('auth_token')->plainTextToken;
        }

        $response = [
            'status' => 'success',
            'messages' => $this->message,
            'data' => $data,
        ];

        return $response;
    }
}
