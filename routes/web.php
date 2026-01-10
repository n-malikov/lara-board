<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Auth::routes();

Route::get('/verify/{token}', [RegisterController::class, 'verify'])->name('register.verify');

Route::get('/cabinet', [App\Http\Controllers\HomeController::class, 'cabinet'])->name('cabinet');
