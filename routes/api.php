<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/email/resend', [AuthController::class, 'resendVerification']);

// Password reset routes (public)
Route::post('/password/email', [AuthController::class, 'sendPasswordResetCode']);
Route::post('/password/reset', [AuthController::class, 'resetPasswordWithCode']);

// Email verification routes (public - signed middleware handles security)
Route::get('/email/verify/{id}/{hash}', [ApiAuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::post('/logout-all-devices', [ApiAuthController::class, 'logoutAllDevices']);
    Route::get('/me', [ApiAuthController::class, 'me']);
    Route::post('/email/resend', [ApiAuthController::class, 'resendVerification']);
    Route::post('/change-password', [ApiAuthController::class, 'changePassword']);
    
    // Session management routes
    Route::get('/sessions', [ApiAuthController::class, 'getActiveSessions']);
    Route::delete('/sessions/{tokenId}', [ApiAuthController::class, 'revokeSession']);
    
    // Chat routes (protected)
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])
        ->middleware(['verified']);
    
    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/users', function () {
            return response()->json(['message' => 'Admin users endpoint']);
        });
    });
    
    // Manager and Admin routes
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/manage/announcements', function () {
            return response()->json(['message' => 'Manage announcements endpoint']);
        });
    });
});