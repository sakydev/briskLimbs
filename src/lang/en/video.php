<?php

return [
    'failed' => [
        'find' => [
            'fetch' => 'Requested video was not found',
            'permissions' => 'User has insufficient to view video',
            'unknown' => 'Something went wrong trying to find video',
        ],
        'update' => [
            'already' => [
                'active' => 'Requested video is already active',
                'inactive' => 'Requested video is already inactive',
                'public' => 'Requested video is already public',
                'private' => 'Requested video is already private',
                'unlisted' => 'Requested video is already unlisted',
            ],
            'permissions' => 'User has insufficient permissions to update video',
            'unknown' => 'Something went wrong trying to update video',
        ],
        'store' => [
            'file' => 'Failed to store video on server',
            'permissions' => 'User has insufficient permissions to upload video',
            'meta' => 'Failed to extract video meta data',
            'unknown' => 'Something went wrong trying to upload video',
        ],
        'delete' => [
            'media' => 'Failed to delete video files and thumbnails',
            'permissions' => 'User has insufficient to delete video',
            'unknown' => 'Something went wrong trying to delete video',
        ],
    ],
    'success' => [
        'find' => [
            'fetch' => 'Requested video fetched successfully',
            'list' => 'Requested videos have been fetched successfully',
            'empty' => 'No videos matched your request',
            'search' => 'Searched videos have been fetched successfully',
        ],
        'update' => [
            'single' => 'Requested video has been updated successfully',
            'list' => 'Requested videos have been updated successfully',
            'activate' => 'Requested video has been activated successfully',
            'deactivate' => 'Requested video has been deactivated successfully',
            'public' => 'Requested video has been made public successfully',
            'private' => 'Requested video has been made private successfully',
            'unlisted' => 'Requested video has been made unlisted successfully',
        ],
        'store' => [
            'single' => 'Video has been saved successfully',
        ],
        'delete' => [
            'single' => 'Requested video has been deleted successfully',
            'list' => 'Requested videos have been deleted successfully',
        ],
    ],
];
