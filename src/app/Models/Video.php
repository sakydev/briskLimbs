<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 *
 * @property int $total_views
 * @property int $total_comments
 *
 * @property int $allow_comments
 * @property int $allow_embed
 * @property int $allow_download
 *
 * @property string[] $original_meta
 */
class Video extends BriskLimbs
{
    use HasFactory;
    use Searchable;

    public const PROCESSING_PENDING = 'pending';
    public const PROCESSING_PROGRESS = 'progress';
    public const PROCESSING_SUCCESS = 'success';
    public const PROCESSING_FAILED = 'failure';

    public const STATE_ACTIVE = 'active';
    public const STATE_INACTIVE = 'inactive';

    public const SCOPE_PUBLIC = 'public';
    public const SCOPE_PRIVATE = 'private';
    public const SCOPE_UNLISTED = 'unlisted';

    public const DEFAULT_THUMBNAIL = 1;

    protected $guarded = [];
    protected $casts = [
        'original_meta' => 'array',
    ];

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
