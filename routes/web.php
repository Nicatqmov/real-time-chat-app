<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('login',[AuthController::class,'login_page'])->name('login');
Route::post('login',[AuthController::class,'login']);
Route::get('signup',[AuthController::class,'signup_page'])->name('signup');
Route::post('signup',[AuthController::class,'signup']);
Route::post('logout',[AuthController::class,'logout'])->name('logout');

Route::post('/chat/send',[ChatController::class,'send']);
Route::get('/',[ChatController::class,'index'])->middleware('auth');
