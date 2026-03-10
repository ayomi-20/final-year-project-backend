<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Test route
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});


Route::post('/register', [AuthController::class, 'register']);

// email verification route
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);