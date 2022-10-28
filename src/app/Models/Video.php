<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property string $vkey
 * @property string $filename
 * @property string $title
 * @property string $description
 *
 * @property string $state
 * @property string $status
 * @property string $scope
 *
 * @property int $duration
 * @property string $directory
 * @property int $default_thumbnail
 * @property string $qualities
 * @property string $tags
 *
 * @property int $total_views
 * @property int $total_comments
 *
 * @property int $allow_comments
 * @property int $allow_embed
 * @property int $allow_download
 *
 * @property string $server_url;
 * @property string[] $original_meta
 *
 * @property Carbon|string $created_at
 * @property Carbon|string $updated_at
 */
class Video extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'original_meta' => 'array',
    ];

    public const VIDEO_PROCESSING_PENDING = 'pending';
    public const VIDEO_PROCESSING_PROGRESS = 'progress';
    public const VIDEO_PROCESSING_SUCCESS = 'success';
    public const VIDEO_PROCESSING_FAILED = 'failure';
}
