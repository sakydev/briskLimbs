<?php declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthenticationController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
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
