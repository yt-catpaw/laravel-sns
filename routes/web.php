<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;


Route::middleware('guest')->group(function () {

    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'show')->name('login');
        Route::post('/login', 'login');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'show')->name('register.show');
        Route::post('/register', 'store')->name('register.store');
    });

    Route::controller(PasswordResetController::class)->group(function () {
        Route::get('/password/reset', 'show')->name('password.reset.show');
        Route::post('/password/reset', 'send')->name('password.reset.send');
    });

});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LogoutController::class, 'logout'])
        ->name('logout');

    Route::get('/', [PostController::class, 'index'])
        ->name('timeline.index');
    
    Route::get('/posts/{post}', [PostController::class, 'show'])
        ->name('posts.show');

    Route::post('/posts', [PostController::class, 'store'])
        ->name('posts.store');

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');
    
    Route::controller(LikeController::class)->group(function () {
        Route::post('/posts/{post}/like', 'store')->name('posts.like');
        Route::delete('/posts/{post}/like', 'destroy')->name('posts.unlike');
    });
});
