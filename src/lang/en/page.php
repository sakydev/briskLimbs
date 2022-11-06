<?php

return [
    'failed' => [
        'find' => [
            'fetch' => 'Requested page was not found',
            'permissions' => 'User has insufficient to view page',
            'unknown' => 'Something went wrong trying to find page',
        ],
        'update' => [
            'already_published' => 'Requested page is already published',
            'already_unpublished' => 'Requested page is already unpublished',
            'permissions' => 'User has insufficient to update page',
            'unknown' => 'Something went wrong trying to update page',
        ],
        'store' => [
            'permissions' => 'User has insufficient to create page',
            'unknown' => 'Something went wrong trying to create page',
        ],
        'delete' => [
            'permissions' => 'User has insufficient to delete page',
            'unknown' => 'Something went wrong trying to delete page',
        ],
    ],
    'success' => [
        'find' => [
            'fetch' => 'Requested page fetched successfully',
            'list' => 'Pages have been fetched successfully',
        ],
        'update' => [
            'single' => 'Requested page has been updated successfully',
            'list' => 'Requested pages have been updated successfully',
            'publish' => 'Requested page has been published successfully',
            'unpublish' => 'Requested page has been unpublished successfully',
        ],
        'store' => [
            'single' => 'Page has been created successfully',
        ],
        'delete' => [
            'single' => 'Requested page has been deleted successfully',
            'list' => 'Requested pages have been deleted successfully',
        ],
    ],
];
