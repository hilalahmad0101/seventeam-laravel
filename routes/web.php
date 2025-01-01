<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.auth');
});


Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories/list', 'index')->name('admin.category.list');
    Route::get('/categories/create', 'create')->name('admin.category.create');
    Route::post('/categories/store', 'store')->name('admin.category.store');
    Route::get('/categories/edit/{id}', 'edit')->name('admin.category.edit');
    Route::post('/categories/update/{id}', 'update')->name('admin.category.update');
    Route::get('/categories/delete/{id}', 'delete')->name('admin.category.delete');
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