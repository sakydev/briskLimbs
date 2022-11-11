<?php

namespace App\Models;

/**
 * @property string $slug
 * @property string $title
 * @property string $state
 * @property string $content
 */
class Page extends BriskLimbs
{
    public const STATE_PUBLISHED = 'publish';
    public const STATE_UNPUBLISHED = 'unpublish';

    protected $fillable = ['title', 'slug', 'content'];

    public function isPublished(): bool {
        return $this->state == self::STATE_PUBLISHED;
    }
}
