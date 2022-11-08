<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

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
    use Searchable;

    protected $guarded = [];
    protected $casts = [
        'original_meta' => 'array',
    ];

    public const PROCESSING_PENDING = 'pending';
    public const PROCESSING_PROGRESS = 'progress';
    public const PROCESSING_SUCCESS = 'success';
    public const PROCESSING_FAILED = 'failure';

    public const STATE_ACTIVE = 'active';
    public const STATE_INACTIVE = 'inactive';

    public const SCOPE_PUBLIC = 'public';
    public const SCOPE_PRIVATE = 'private';
    public const SCOPE_UNLISTED = 'unlisted';

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function getOriginalWidth(): ?int {
        return $this->original_meta['width'] ?? null;
    }

    public function getOriginalHeight(): ?int {
        return $this->original_meta['height'] ?? null;
    }
}
