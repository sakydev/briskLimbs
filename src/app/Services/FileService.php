<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    private const DIRECTORY_TYPE_TEMPORARY = 'temporary';
    private const DIRECTORY_TYPE_TEMPORARY_VIDEOS = 'temporary/videos';
    private const DIRECTORY_TYPE_VIDEOS = 'public/videos';
    private const DIRECTORY_TYPE_THUMBNAILS = 'public/thumbnails';

    public static function getDatedDirectoryName(): string {
        return date('Y/m/d');
    }

    public static function createDirectory(string $directory, string $disk = 'local'): ?string {
        $created = Storage::disk($disk)->makeDirectory($directory);
        if ($created) {
            return $directory;
        }

        return null;
    }

    public static function createTemporaryDirectory(): ?string {
        return self::createDirectory(self::DIRECTORY_TYPE_TEMPORARY, '');
    }

    public static function createTemporaryVideosDirectory(): ?string {
        return self::createDirectory(self::DIRECTORY_TYPE_TEMPORARY_VIDEOS, '');
    }

    public static function createVideosDirectory(): string {
        $directory = sprintf(
            '%s/%s',
            self::DIRECTORY_TYPE_VIDEOS,
            self::getDatedDirectoryName()
        );

        return self::createDirectory($directory);
    }

    public static function createThumbnailsDirectory(): ?string {
        $directory = sprintf(
            '%s/%s',
            self::DIRECTORY_TYPE_THUMBNAILS,
            self::getDatedDirectoryName()
        );

        return self::createDirectory($directory);
    }

    public static function createMediaDestinationDirecctories(): array {
        return [
            'videos' => self::createVideosDirectory(),
            'thumbnails' => self::createThumbnailsDirectory(),
        ];
    }

    public static function getDirectory(string $directory): string {
        return storage_path("app/{$directory}");
    }

    public static function getTemporaryDirectory(): string {
        return self::getDirectory(self::DIRECTORY_TYPE_TEMPORARY);
    }

    public static function getTemporaryVideosDirectory(): string {
        return self::getDirectory(self::DIRECTORY_TYPE_TEMPORARY_VIDEOS);
    }

    public static function getVideosDirectory(): string {
        return self::getDirectory(self::DIRECTORY_TYPE_VIDEOS);
    }

    public static function getThumbnailsDirectory(): string {
        return self::getDirectory(self::DIRECTORY_TYPE_THUMBNAILS);
    }

    public static function getTemporaryVideo(string $filename): string {
        return sprintf('%s/%s', self::getTemporaryVideosDirectory(), $filename);
    }

    public static function getVideo(string $dated, string $filename): string {
        return sprintf('%s/%s/%s', self::getVideosDirectory(), $dated, $filename);
    }
}
