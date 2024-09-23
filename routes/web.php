<?php

use Illuminate\Support\Facades\Route;

Route::get('/redirect-google', function () {
    return view('auth.google_callback');
});
