<?php

use App\Models\Product;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;

Route::get('reset/{token}', [ApiController::class, 'resetPasswordPage']);
Route::post('submitPassword', [ApiController::class, 'submitPassword'])->name('submitPassword');
Auth::routes();

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('postLogin', [AuthController::class, 'postLogin'])->name('postLogin');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('postRegister', [AuthController::class, 'postRegister'])->name('postRegister');

Route::get('/otp', [AuthController::class, 'otpPage'])->name('otpPage');
Route::post('postOtp', [AuthController::class, 'postOtp'])->name('postOtp');
Route::get('/resend/otp', [AuthController::class, 'resendOtp'])->name('resendOtp');

Route::get('/s', [AuthController::class, 'session_flush']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

//user
Route::get('/user', [AuthController::class, 'userDashboard'])->name('userDashboard');

