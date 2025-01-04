<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('layouts.auth');
// });

Route::middleware(['guest'])->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'index')->name('admin.auth.login');
        Route::post('/login', 'login')->name('admin.login');
    });
});

Route::middleware(['auth'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('admin.dashboard');
        Route::get('/logout', 'logout')->name('admin.logout');
        Route::get('/user/list', 'user_list')->name('admin.user.list');
        Route::get('/user/delete/{id}', 'user_delete')->name('admin.user.delete');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories/list', 'index')->name('admin.category.list');
        Route::get('/categories/create', 'create')->name('admin.category.create');
        Route::post('/categories/store', 'store')->name('admin.category.store');
        Route::get('/categories/edit/{id}', 'edit')->name('admin.category.edit');
        Route::post('/categories/update/{id}', 'update')->name('admin.category.update');
        Route::get('/categories/delete/{id}', 'delete')->name('admin.category.delete');
    });


    Route::controller(BannerController::class)->group(function () {
        Route::get('/banner/list', 'index')->name('admin.banner.list');
        Route::get('/banner/create', 'create')->name('admin.banner.create');
        Route::post('/banner/store', 'store')->name('admin.banner.store');
        Route::get('/banner/edit/{id}', 'edit')->name('admin.banner.edit');
        Route::post('/banner/update/{id}', 'update')->name('admin.banner.update');
        Route::get('/banner/delete/{id}', 'delete')->name('admin.banner.delete');
    });

    Route::controller(VideoController::class)->group(function () {
        Route::get('/video/list', 'index')->name('admin.video.list');
        Route::get('/video/create', 'create')->name('admin.video.create');
        Route::post('/upload-chunk', 'uploadChunk')->name('admin.video.store');
        Route::post('/stop-upload', 'stopUpload')->name('admin.video.stop');
        Route::post('/resume-upload', 'resumeUpload')->name('admin.video.resume');
        Route::post('/get-upload-progress', 'getUploadProgress');
        Route::post('/delete-video', 'deleteVideo')->name('admin.video.delete');
    });
});