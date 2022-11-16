<?php declare(strict_types=1);

use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\Search\VideoSearchController;
use App\Http\Controllers\Api\V1\VideoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\VideoStateController;
use App\Http\Controllers\Api\V1\VideoScopeController;

Route::prefix('videos')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('videos.list');
    Route::get('/search', [VideoSearchController::class, 'search'])->name('videos.search');
    Route::get('/{videoId}', [VideoController::class, 'show'])->name('videos.show');
    Route::get('/{videoId}/comments', [CommentController::class, 'index'])
        ->name('videos.comments.list');
    Route::get('/{videoId}/comments/{commentId}', [CommentController::class, 'show'])
        ->name('videos.comments.show');

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

        Route::post('/{videoId}/comments', [CommentController::class, 'store'])
            ->name('videos.comments.store');
        Route::put('/{videoId}/comments/{commentId}', [CommentController::class, 'update'])
            ->name('videos.comments.update');
        Route::delete('/{videoId}/comments/{commentId}', [CommentController::class, 'destroy'])
            ->name('videos.comments.delete');
    });
});
