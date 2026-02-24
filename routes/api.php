<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/resend', [AuthController::class, 'resendVerification']);

// Email verification routes (public - signed middleware handles security)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);
    
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