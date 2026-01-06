<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentCountController;

Route::get('/comments/count', CommentCountController::class);
