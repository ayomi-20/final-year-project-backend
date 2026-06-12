<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\StatsController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// ── Auth routes ────────────────────────────────────────────────────────────
Route::post('/register',           [AuthController::class, 'register']);
Route::post('/login',              [AuthController::class, 'login']);
Route::post('/verify-login-otp',   [AuthController::class, 'verifyLoginOtp']);
Route::post('/forgot-password',    [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-code',  [AuthController::class, 'verifyResetCode']);
Route::post('/reset-password',     [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
// Route::patch('/provider/update', [ProviderController::class, 'update']);

// ── Public routes ──────────────────────────────────────────────────────────
Route::get('/home',                         [HomeController::class, 'index']);
Route::get('/services',                     [ServiceController::class, 'index']);
Route::get('/services/{slug}',              [ServiceController::class, 'show']);
Route::get('/services/{serviceId}/reviews', [ReviewController::class, 'index']);
Route::get('categories', fn() => \App\Models\Category::all(['id', 'name', 'slug']));

// ── Authenticated routes ───────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Tourist bookings
    Route::post('/tourist/bookings',                [BookingController::class, 'store']);
    Route::get('/tourist/bookings',                 [BookingController::class, 'index']);
    Route::patch('/tourist/bookings/{id}/cancel',   [BookingController::class, 'cancel']);

    // Provider booking management
    Route::patch('/provider/bookings/{id}',         [BookingController::class, 'updateStatus']);

    // Reviews
    Route::post('/tourist/reviews',                 [ReviewController::class, 'store']);

    // Provider registration
    Route::post('/provider/register',               [ProviderController::class, 'register']);
    Route::post('/provider/documents',              [ProviderController::class, 'uploadDocuments']);
    Route::post('/provider/submit',                 [ProviderController::class, 'submit']);
    Route::patch('/provider/update', [ProviderController::class, 'update']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/provider/profile',                 [ProviderController::class, 'profile']);

    // Stats
    Route::get('/tourist/stats',                    [StatsController::class, 'touristStats']);
    Route::get('/provider/stats',                   [StatsController::class, 'providerStats']);
    Route::get('/admin/stats',                      [StatsController::class, 'adminStats']);
});