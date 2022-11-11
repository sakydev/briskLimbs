<?php

use App\Http\Controllers\Api\V1\SettingController;
use Illuminate\Support\Facades\Route;

Route::put('settings', [SettingController::class, 'update'])
    ->middleware('auth:sanctum')
    ->name('videos.makeUnlisted');
