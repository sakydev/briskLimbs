<?php

namespace Database\Factories;

use App\Models\Video;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = [Video::STATE_ACTIVE, Video::STATE_INACTIVE];
        $scopes = [Video::SCOPE_PUBLIC, Video::SCOPE_PRIVATE, Video::SCOPE_UNLISTED];

        return [
            'title' => fake()->realText(50),
            'description' => fake()->realText(100),
            'user_id' => 1,
            'category_id' => 1,
            'vkey' => str_replace(' ', '', fake()->text(14)),
            'filename' => str_replace(' ', '', fake()->text(14)),
            'state' => $states[array_rand($states)],
            'status' => Video::PROCESSING_SUCCESS,
            'scope' => $scopes[array_rand($scopes)],
            'directory' => FileService::getDatedDirectoryName(),
            'converted_at' => Carbon::now(),
        ];
    }
}
