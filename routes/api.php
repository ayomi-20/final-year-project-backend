<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::post('/register',         [AuthController::class, 'register']);
Route::post('/login',            [AuthController::class, 'login']);
Route::post('/verify-login-otp', [AuthController::class, 'verifyLoginOtp']);

Route::post('/forgot-password',    [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-code',  [AuthController::class, 'verifyResetCode']);
Route::post('/reset-password',     [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);