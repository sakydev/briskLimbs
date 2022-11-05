<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AuthenticationController;
use App\Http\Controllers\Api\V1\VideoController;
use App\Http\Controllers\Api\V1\VideoStateController;
use App\Http\Controllers\Api\V1\VideoScopeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('V1/users')->group(function () {
    Route::post('/', [AuthenticationController::class, 'register'])->name('users.register');
    Route::post('/login', [AuthenticationController::class, 'login'])->name('users.login');

    Route::get('/', [UserController::class, 'index'])->name('users.list');
    Route::get('/{userId}', [UserController::class, 'show'])->name('users.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/{userId}', [UserController::class, 'update'])->name('users.update');
        Route::put('/{userId}/activate', [UserController::class, 'activate'])
            ->name('users.activate');
        Route::put('/{userId}/deactivate', [UserController::class, 'deactivate'])
            ->name('users.deactivate');
    });
});

Route::prefix('V1/videos')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('videos.list');
    Route::get('/{videoId}', [VideoController::class, 'show'])->name('videos.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [VideoController::class, 'store'])->name('videos.store');
        Route::put('/{videoId}', [VideoController::class, 'update'])->name('videos.update');

        Route::put('/{videoId}/activate', [VideoStateController::class, 'activate'])
            ->name('videos.activate');
        Route::put('/{videoId}/deactivate', [VideoStateController::class, 'deactivate'])
            ->name('videos.deactivate');

        Route::put('/{videoId}/public', [VideoScopeController::class, 'public'])
            ->name('videos.makePublic');
        Route::put('/{videoId}/private', [VideoScopeController::class, 'private'])
            ->name('videos.makePrivate');
        Route::put('/{videoId}/unlisted', [VideoScopeController::class, 'unlisted'])
            ->name('videos.makeUnlisted');
    });
});
