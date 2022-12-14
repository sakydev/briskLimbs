<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Video;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('value');
            $table->timestamps();
        });

        $settings = [
            'title' => 'BriskLimbs',
            'description' => 'Fastest open source video sharing software',
            'theme' => 'default',
            'directory' => '',
            'url' => 'http://localhost',
            'title_separator' => '-',
            'supported_formats_video' => '.mp4,.mpeg,.mov,.wmv,.flv',
            'supported_formats_avatar' => 'jpg,png,jpeg',
            'supported_formats_cover' => 'jpg,png,jpeg',
            'max_filesize_video' => 10000, // 10MB
            'max_filesize_avatar' => 2000, // 2MB
            'max_filesize_cover' => 3000, // 3MB
            'max_filesize_thumbnail' => 3000, // 3MB
            'max_thumbnails_count' => 5,
            'placeholder_text' => 'Enter value',
            'placeholder_email' => 'snow@thewall.com',
            'placeholder_textarea' => 'Enter your input',
            'placeholder_integer' => 'Enter numerical value',
            'default_video_quality' => 360,
            'default_video_scope' => Video::SCOPE_PUBLIC,
            'default_video_state' => Video::STATE_ACTIVE,
            'default_video_extension' => 'mp4',
            'default_thumbnail' => Video::DEFAULT_THUMBNAIL,
            'default_thumbnail_extension' => 'jpg',
            'path_php' => '/usr/bin/php',
            'path_ffmpeg' => '/usr/bin/ffmpeg',
            'path_ffprobe' => '/usr/bin/ffprobe',
            'allow_search' => 1,
            'allow_uploads' => 1,
            'allow_registrations' => 1,
            'allow_comments' => 1,
            'allow_embeds' => 1,
            'allow_downloads' => 0,
            'allow_avatars' => 1,
            'allow_covers' => 1,
            'allow_video_watermark' => 0,
            'allow_qualities' => '240,360',
            'allow_ffmpeg_preclip' => 0,
            'allow_ffmpeg_postclip' => 0,
            'allow_login_twitter' => 0,
            'allow_login_google' => 0,
            'max_results_comment' => 12,
            'max_results_page' => 12,
            'max_results_video' => 12,
            'max_results_user' => 12,
            'ffmpeg_preset' => 'medium',
            'ffmpeg_vcodec' => 'libx264',
            'ffmpeg_acodec' => 'libfdk_aac',
            'ffmpeg_vbitrate_240' => 576,
            'ffmpeg_vbitrate_360' => 896,
            'ffmpeg_vbitrate_480' => 1536,
            'ffmpeg_vbitrate_720' => 3072,
            'ffmpeg_vbitrate_1080' => 4992,
            'ffmpeg_abitrate_240' => 64,
            'ffmpeg_abitrate_360' => 64,
            'ffmpeg_abitrate_480' => 96,
            'ffmpeg_abitrate_720' => 96,
            'ffmpeg_abitrate_1080' => 128,
            'ffmpeg_watermark_placement' => 'bottom:right',
            'ffmpeg_preclip' => '',
            'ffmpeg_postclip' => '',
        ];

        foreach ($settings as $name => $value) {
            Setting::create([
                'name' => $name,
                'value' => $value,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
