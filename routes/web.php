<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\authController;
Route::get('/', function () {
    return view('welcome');
});


