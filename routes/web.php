<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login'); // we’ll create this view next
});

Route::post('/login', [LoginController::class, 'login'])->name('login');