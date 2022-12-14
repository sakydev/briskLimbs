<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $name
 * @property string $description
 * @property string $state
 */
class Category extends BriskLimbs
{
    use HasFactory;
    public const STATE_PUBLISHED = 'publish';
    public const STATE_UNPUBLISHED = 'unpublish';

    protected $fillable = ['name', 'description', 'state'];

    public function isPublished(): bool {
        return $this->state === self::STATE_PUBLISHED;
    }
}
