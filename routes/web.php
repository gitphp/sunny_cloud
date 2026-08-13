<?php

use App\Http\Controllers\frontend\IndexController as FrontendIndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendIndexController::class, 'index'])->name('home');
Route::get('/company/{any?}', [FrontendIndexController::class, 'index'])
    ->where('any', '.*')
    ->name('company');
Route::get('/apply/{any?}', [FrontendIndexController::class, 'index'])
    ->where('any', '.*')
    ->name('apply');

Route::redirect('/frontend/home', '/', 301);
Route::redirect('/frontend', '/', 301);
