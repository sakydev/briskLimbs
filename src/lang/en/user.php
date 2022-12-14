<?php

return [
    'failed' => [
        'find' => [
            'fetch' => 'Requested user was not found',
            'permissions' => 'User has insufficient permissions to view user',
            'unknown' => 'Something went wrong trying to find user',
        ],
        'update' => [
            'already' => [
                'active' => 'Requested user is already active',
                'inactive' => 'Requested user is already inactive',
            ],
            'permissions' => 'User has insufficient permissions to update',
            'unknown' => 'Something went wrong trying to update user',
        ],
        'store' => [
            'restricted' => 'Registrations are not allowed at the moment',
            'permissions' => 'You are now allowed to create an account',
            'unknown' => 'Something went wrong trying to create user',
        ],
        'delete' => [
            'permissions' => 'User has insufficient to delete user',
            'unknown' => 'Something went wrong trying to delete user',
        ],
    ],
    'success' => [
        'find' => [
            'fetch' => 'Requested user fetched successfully',
            'list' => 'Users have been fetched successfully',
        ],
        'update' => [
            'single' => 'Requested user has been updated successfully',
            'list' => 'Requested users have been updated successfully',
            'activate' => 'Requested user has been activated successfully',
            'deactivate' => 'Requested user has been deactivated successfully',
        ],
        'store' => [
            'single' => 'User has been created successfully',
        ],
        'delete' => [
            'single' => 'Requested user has been deleted successfully',
            'list' => 'Requested users have been deleted successfully',
        ],
        'login' => 'User logged in successfully',
    ],
];
