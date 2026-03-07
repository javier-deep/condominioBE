<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'apartment_number' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'apartment_number' => $request->apartment_number,
            'address' => $request->address,
        ]);

        // Assign default resident role
        $residentRole = Role::where('name', 'resident')->first();
        if ($residentRole) {
            $user->roles()->attach($residentRole);
        }

        // Trigger email verification
        event(new Registered($user));

        return response()->json([
            'message' => 'Usuario registrado exitosamente. Por favor verifica tu correo electrónico antes de iniciar sesión.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at
            ],
            'requires_verification' => true
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Cuenta desactivada'
            ], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Debes verificar tu correo electrónico antes de iniciar sesión.',
                'requires_verification' => true,
                'user_id' => $user->id
            ], 403);
        }

        // Obtener información del dispositivo
        $userAgent = $request->header('User-Agent', 'Unknown Device');
        $deviceName = $this->getDeviceName($userAgent);
        $ipAddress = $request->ip();
        
        // Crear token con nombre único por dispositivo
        $tokenName = $deviceName . ' - ' . $ipAddress . ' - ' . now()->format('Y-m-d H:i:s');
        $tokenResult = $user->createToken($tokenName);
        
        // Actualizar el token con información del dispositivo
        $tokenModel = $tokenResult->accessToken;
        $tokenModel->device_name = $deviceName;
        $tokenModel->ip_address = $ipAddress;
        $tokenModel->user_agent = $userAgent;
        $tokenModel->logged_in_at = now();
        $tokenModel->save();

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user->load('roles'),
            'token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
            'device_info' => [
                'device_name' => $deviceName,
                'ip_address' => $ipAddress,
                'logged_in_at' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Logout user (cerrar sesión en este dispositivo)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Logout from all devices (cerrar sesión en todos los dispositivos)
     */
    public function logoutAllDevices(Request $request)
    {
        // Eliminar todos los tokens del usuario
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices successfully'
        ]);
    }

    /**
     * Get active sessions (obtener dispositivos activos)
     */
    public function getActiveSessions(Request $request)
    {
        $user = $request->user();
        $currentToken = $request->user()->currentAccessToken();
        
        $sessions = $user->tokens->map(function ($token) use ($currentToken) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'device_name' => $token->device_name ?? 'Unknown Device',
                'ip_address' => $token->ip_address ?? 'Unknown IP',
                'user_agent' => $token->user_agent ?? 'Unknown User Agent',
                'logged_in_at' => $token->logged_in_at ?? $token->created_at,
                'last_used_at' => $token->last_used_at,
                'is_current' => $token->id === $currentToken->id
            ];
        });

        return response()->json([
            'sessions' => $sessions
        ]);
    }

    /**
     * Revoke specific session (cerrar sesión en dispositivo específico)
     */
    public function revokeSession(Request $request, $tokenId)
    {
        $user = $request->user();
        $token = $user->tokens()->where('id', $tokenId)->first();
        
        if (!$token) {
            return response()->json([
                'message' => 'Session not found'
            ], 404);
        }
        
        // No permitir cerrar la sesión actual mediante este endpoint
        if ($token->id === $request->user()->currentAccessToken()->id) {
            return response()->json([
                'message' => 'Cannot revoke current session. Use logout instead.'
            ], 422);
        }
        
        $token->delete();
        
        return response()->json([
            'message' => 'Session revoked successfully'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('roles')
        ]);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            // event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request)
    {
        // Si viene user_id en el request (desde login fallido)
        if ($request->user_id) {
            $user = User::findOrFail($request->user_id);
        } else {
            // Si es un usuario autenticado
            $user = $request->user();
        }

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email ya verificado'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación enviado']);
    }

    /**
     * Change password and logout all devices
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Cambiar contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Cerrar sesión en todos los dispositivos
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password changed successfully. You have been logged out from all devices.'
        ]);
    }

    /**
     * Get device name from User Agent
     */
    private function getDeviceName($userAgent)
    {
        // Detectar dispositivos móviles
        if (preg_match('/Mobile|Android|iPhone|iPad|Windows Phone/', $userAgent)) {
            if (strpos($userAgent, 'iPhone') !== false) {
                return 'iPhone';
            } elseif (strpos($userAgent, 'iPad') !== false) {
                return 'iPad';
            } elseif (strpos($userAgent, 'Android') !== false) {
                return 'Android Device';
            } elseif (strpos($userAgent, 'Windows Phone') !== false) {
                return 'Windows Phone';
            } else {
                return 'Mobile Device';
            }
        }

        // Detectar navegadores de escritorio
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome Browser';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox Browser';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            return 'Safari Browser';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge Browser';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            return 'Opera Browser';
        } else {
            return 'Desktop Browser';
        }
    }
}
