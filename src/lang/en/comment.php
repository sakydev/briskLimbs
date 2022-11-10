<?php

return [
    'failed' => [
        'find' => [
            'fetch' => 'Requested comment was not found',
            'permissions' => 'User has insufficient to view comment',
            'unknown' => 'Something went wrong trying to find comment',
        ],
        'update' => [
            'permissions' => 'User has insufficient to update comment',
            'unknown' => 'Something went wrong trying to update comment',
        ],
        'store' => [
            'permissions' => 'User has insufficient to create comment',
            'unknown' => 'Something went wrong trying to create comment',
        ],
        'delete' => [
            'permissions' => 'User has insufficient to delete comment',
            'unknown' => 'Something went wrong trying to delete comment',
        ],
    ],
    'success' => [
        'find' => [
            'fetch' => 'Requested comment fetched successfully',
            'list' => 'Comments have been fetched successfully',
        ],
        'update' => [
            'single' => 'Requested comment has been updated successfully',
            'list' => 'Requested comments have been updated successfully',
        ],
        'store' => [
            'single' => 'Comment has been created successfully',
        ],
        'delete' => [
            'single' => 'Requested comment has been deleted successfully',
            'list' => 'Requested comments have been deleted successfully',
        ],
    ],
];
