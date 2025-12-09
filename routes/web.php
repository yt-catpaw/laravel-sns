<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;


Route::middleware('guest')
    ->controller(LoginController::class)
    ->group(function () {
        Route::get('/login', 'show')->name('login');
        Route::post('/login', 'login');
    });
Route::middleware('guest')
    ->controller(RegisterController::class)
    ->group(function () {
        Route::get('/register', 'show')->name('register.show');
        Route::post('/register', 'store')->name('register.store');
    });
Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return view('timeline.index'); 
})->middleware('auth');
