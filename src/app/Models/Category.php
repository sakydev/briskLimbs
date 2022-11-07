<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $description
 * @property string $state
 *
 * @property Carbon|string $created_at
 * @property Carbon|string $updated_at
 */
class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public const STATE_PUBLISHED = 'publish';
    public const STATE_UNPUBLISHED = 'unpublish';

    public function isPublished(): bool {
        return $this->state === self::STATE_PUBLISHED;
    }
}
