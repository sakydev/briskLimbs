<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
            'max_filesize_video' => 100,
            'max_filesize_avatar' => 2,
            'max_filesize_cover' => 3,
	        'max_filesize_thumbnail' => 3,
            'placeholder_text' => 'Enter value',
            'placeholder_email' => 'snow@thewall.com',
            'placeholder_textarea' => 'Enter your input',
            'placeholder_integer' => 'Enter numerical value',
            'default_video_quality' => 360,
            'default_video_scope' => 'public',
            'default_thumbnail' => 3,
            'path_php' => '/usr/bin/php',
            'path_ffmpeg' => '/usr/bin/ffmpeg',
            'path_ffprobe' => '/usr/bin/ffprobe',
            'allow_search' => 1,
            'allow_uploads' => 1,
            'allow_registrations' => 1,
            'allow_comments' => 1,
            'allow_embeds' => 1,
            'allow_avatars' => 1,
            'allow_covers' => 1,
            'allow_video_watermark' => 0,
            'allow_video_240' => 1,
            'allow_video_360' => 1,
            'allow_video_480' => 1,
            'allow_video_720' => 1,
            'allow_video_1080' => 1,
            'allow_ffmpeg_preclip' => 0,
            'allow_ffmpeg_postclip' => 0,
            'allow_login_twitter' => 0,
            'allow_login_google' => 0,
            'max_results_home' => 12,
            'max_results_search' => 10,
            'max_results_channel' => 12,
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
}
