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
// auth:sanctum  → valida token valido (sesion activa)
// active        → valida que la cuenta no este desactivada
Route::middleware(['auth:sanctum', 'active'])->group(function () {
    // Auth / sesion
    Route::post('/logout',              [ApiAuthController::class, 'logout']);
    Route::post('/logout-all-devices',  [ApiAuthController::class, 'logoutAllDevices']);
    Route::get('/me',                   [ApiAuthController::class, 'me']);
    Route::post('/email/resend',        [ApiAuthController::class, 'resendVerification']);
    Route::post('/change-password',     [ApiAuthController::class, 'changePassword']);

    // Gestion de sesiones del dispositivo
    Route::get('/sessions',                    [ApiAuthController::class, 'getActiveSessions']);
    Route::delete('/sessions/{tokenId}',       [ApiAuthController::class, 'revokeSession']);

    // Chat (requiere ademas email verificado)
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])
        ->middleware(['verified']);

    // Solo Admin
    Route::middleware(['ensure.role:admin'])->group(function () {
        Route::get('/admin/users', function () {
            return response()->json(['message' => 'Admin users endpoint']);
        });
    });

    // Admin o Manager
    Route::middleware(['ensure.role:admin,manager'])->group(function () {
        Route::get('/manage/announcements', function () {
            return response()->json(['message' => 'Manage announcements endpoint']);
        });
    });
});