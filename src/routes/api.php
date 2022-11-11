<?php

use Illuminate\Support\Facades\Route;

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
Route::prefix('V1/')->group(function () {
    Route::group([], base_path('routes/api-group/video.php'));
    Route::group([], base_path('routes/api-group/user.php'));
    Route::group([], base_path('routes/api-group/page.php'));
    Route::group([], base_path('routes/api-group/category.php'));
    Route::group([], base_path('routes/api-group/setting.php'));
});
