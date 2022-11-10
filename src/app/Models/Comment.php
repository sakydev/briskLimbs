<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $video_id
 * @property string $content
 *
 * @property Carbon|string $created_at
 * @property Carbon|string $updated_at
 */
class Comment extends Model
{
}
