<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const VIDEO_PROCESSING_PENDING = 'pending';
    public const VIDEO_PROCESSING_PROGRESS = 'progress';
    public const VIDEO_PROCESSING_SUCCESS = 'success';
    public const VIDEO_PROCESSING_FAILED = 'failure';
}
