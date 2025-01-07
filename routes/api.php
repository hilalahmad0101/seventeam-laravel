<?php

use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Middleware\LogVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware([LogVisitor::class])->group(function () {

    Route::prefix('user')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('email/verification', [AuthController::class, 'emailVerification']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forget/password', [AuthController::class, 'forgetPassword']);
        Route::post('forget/email/verification', [AuthController::class, 'forgetPasswordVerification']);
    });


    Route::post('/user/resend/email', [AuthController::class, 'resendEmail'])
        ->middleware('throttle:1440,3');

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return request()->user();
    });
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::post('reset/password', [AuthController::class, 'resetPassword']);
        });
        Route::controller(HomeController::class)->group(function () {
            Route::get('/video/list', 'listOfMovies');
            Route::get('/videos/with/categories/list', 'listMoviesByCategory');
            Route::get('/videos/details/{id}', 'movieDetails');
            Route::get('/stream/video/{id}', 'streamVideo');
            Route::get('/search/video', 'searchVideos');
            Route::get('/get/banners', 'getBanner');
        });
    });
});