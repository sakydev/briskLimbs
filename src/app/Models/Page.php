<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $slug
 * @property string $title
 * @property string $state
 * @property string $content
 *
 * @property Carbon|string $created_at
 * @property Carbon|string $updated_at
 */
class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content'];

    public const STATE_PUBLISHED = 'publish';
    public const STATE_UNPUBLISHED = 'unpublish';

    public function isPublished(): bool {
        return $this->state == self::STATE_PUBLISHED;
    }
}
