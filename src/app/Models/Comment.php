<?php declare(strict_types=1);

namespace App\Models;

/**
 * @property int $user_id
 * @property int $video_id
 * @property string $content
 */
class Comment extends BriskLimbs
{
    protected $fillable = ['user_id', 'video_id', 'content'];
}
