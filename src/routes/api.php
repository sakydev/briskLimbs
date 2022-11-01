<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AuthenticationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
