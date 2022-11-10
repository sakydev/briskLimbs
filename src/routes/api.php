<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AuthenticationController;
use App\Http\Controllers\Api\V1\VideoController;
use App\Http\Controllers\Api\V1\VideoStateController;
use App\Http\Controllers\Api\V1\VideoScopeController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PageStateController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CategoryStateController;
use App\Http\Controllers\Api\V1\Search\VideoSearchController;
use App\Http\Controllers\CommentController;

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
    Route::get('/search', [VideoSearchController::class, 'search'])->name('videos.search');
    Route::get('/{videoId}', [VideoController::class, 'show'])->name('videos.show');
    Route::get('/{videoId}/comments', [CommentController::class, 'index'])->name('videos.comments.list');

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

        Route::delete('/{videoId}', [VideoController::class, 'delete'])
            ->name('videos.delete');

        Route::post('/{videoId}/comments', [CommentController::class, 'store'])->name('videos.comments.store');
        Route::put('/{videoId}/comments/{commentId}', [CommentController::class, 'update'])->name('videos.comments.update');
    });
});

Route::prefix('V1/pages')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('pages.list');
    Route::get('/{pageId}', [PageController::class, 'show'])->name('pages.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [PageController::class, 'store'])->name('pages.store');
        Route::delete('/{pageId}', [PageController::class, 'destroy'])->name('pages.delete');

        Route::put('/{pageId}', [PageController::class, 'update'])->name('pages.update');
        Route::put('/{pageId}/publish', [PageStateController::class, 'publish'])->name('pages.publish');
        Route::put('/{pageId}/unpublish', [PageStateController::class, 'unpublish'])->name('pages.unpublish');

    });
});

Route::prefix('V1/categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.list');
    Route::get('/{categoryId}', [CategoryController::class, 'show'])->name('categories.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/{categoryId}', [CategoryController::class, 'destroy'])->name('categories.delete');

        Route::put('/{categoryId}', [CategoryController::class, 'update'])->name('categories.update');
        Route::put('/{categoryId}/publish', [CategoryStateController::class, 'publish'])->name('categories.publish');
        Route::put('/{categoryId}/unpublish', [CategoryStateController::class, 'unpublish'])->name('categories.unpublish');

    });
});

Route::put('V1/settings', [SettingController::class, 'update'])
    ->middleware('auth:sanctum')
    ->name('videos.makeUnlisted');
