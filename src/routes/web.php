<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::view('/', 'home');

Route::view('/blank', 'blank');
Route::view('/admin/blank', 'admin/blank');

Route::prefix('videos')->middleware('auth')->group(function () {
    Route::post('/', [VideoController::class, 'store'])->name('store_video');
    Route::put('/{video}', [VideoController::class, 'update'])->name('update_video');
    Route::delete('/{video}', [VideoController::class, 'destroy'])->name('destroy_video');

    Route::get('/upload', [VideoController::class, 'create'])->name('upload_video');
});

Route::prefix('videos')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('list_videos');
    Route::get('/{video}', [VideoController::class, 'show'])->name('show_video');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/pages/{page}', [PageController::class, 'show'])->name('view-page');
