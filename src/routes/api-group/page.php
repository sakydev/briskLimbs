<?php

use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PageStateController;
use Illuminate\Support\Facades\Route;

Route::prefix('pages')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('pages.list');
    Route::get('/{pageId}', [PageController::class, 'show'])->name('pages.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [PageController::class, 'store'])->name('pages.store');
        Route::delete('/{pageId}', [PageController::class, 'destroy'])
            ->name('pages.delete');

        Route::put('/{pageId}', [PageController::class, 'update'])
            ->name('pages.update');
        Route::put('/{pageId}/publish', [PageStateController::class, 'publish'])
            ->name('pages.publish');
        Route::put('/{pageId}/unpublish', [PageStateController::class, 'unpublish'])
            ->name('pages.unpublish');

    });
});
