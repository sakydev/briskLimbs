<?php

return [
    'failed' => [
        'find' => [
            'fetch' => 'Requested category was not found',
            'permissions' => 'User has insufficient to view category',
            'unknown' => 'Something went wrong trying to find category',
        ],
        'update' => [
            'already' => [
                'published' => 'Requested category is already published',
                'unpublished' => 'Requested category is already unpublished',
            ],
            'permissions' => 'User has insufficient to update category',
            'unknown' => 'Something went wrong trying to update category',
        ],
        'store' => [
            'permissions' => 'User has insufficient to create category',
            'unknown' => 'Something went wrong trying to create category',
        ],
        'delete' => [
            'permissions' => 'User has insufficient to delete category',
            'unknown' => 'Something went wrong trying to delete category',
        ],
    ],
    'success' => [
        'find' => [
            'fetch' => 'Requested category fetched successfully',
            'list' => 'Categories have been fetched successfully',
        ],
        'update' => [
            'single' => 'Requested category has been updated successfully',
            'list' => 'Requested categories have been updated successfully',
            'publish' => 'Requested category has been published successfully',
            'unpublish' => 'Requested category has been unpublished successfully',
        ],
        'store' => [
            'single' => 'Category has been created successfully',
        ],
        'delete' => [
            'single' => 'Requested category has been deleted successfully',
            'list' => 'Requested categories have been deleted successfully',
        ],
    ],
];
