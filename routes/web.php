<?php

use App\Http\Controllers\frontend\IndexController as FrontendIndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendIndexController::class, 'index'])->name('home');

Route::redirect('/frontend/home', '/', 301);
Route::redirect('/frontend', '/', 301);
