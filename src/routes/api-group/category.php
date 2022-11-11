<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CategoryStateController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.list');
    Route::get('/{categoryId}', [CategoryController::class, 'show'])->name('categories.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store');
        Route::delete('/{categoryId}', [CategoryController::class, 'destroy'])
            ->name('categories.delete');

        Route::put('/{categoryId}', [CategoryController::class, 'update'])
            ->name('categories.update');
        Route::put('/{categoryId}/publish', [CategoryStateController::class, 'publish'])
            ->name('categories.publish');
        Route::put('/{categoryId}/unpublish', [CategoryStateController::class, 'unpublish'])
            ->name('categories.unpublish');

    });
});
