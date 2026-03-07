<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetCode;
use App\Mail\PasswordResetCodeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // 🔥 CORREGIDO
            'role' => 'user',
        ]);

        event(new Registered($user));

        return response()->json([
            'message' => 'Registered. Check your email to verify.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credentials invalid'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();
        return response()->json(['message' => 'Email verified']);
    }

    public function resend(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent']);
    }

    /**
     * Enviar código de recuperación de contraseña
     */
    public function sendPasswordResetCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Generar código de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Eliminar códigos anteriores para este email
        PasswordResetCode::where('email', $request->email)->delete();

        // Crear nuevo código
        $resetCode = PasswordResetCode::create([
            'email' => $request->email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15) // Expira en 15 minutos
        ]);

        // Enviar email con el código
        try {
            Mail::to($user->email)->send(new PasswordResetCodeMail([
                'code' => $code,
                'name' => $user->name,
                'email' => $user->email
            ]));

            return response()->json(['message' => 'Código de recuperación enviado por email']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al enviar email: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Verificar código y resetear contraseña
     */
    public function resetPasswordWithCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Buscar el código
        $resetCode = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$resetCode) {
            return response()->json(['message' => 'Código inválido'], 400);
        }

        if ($resetCode->hasExpired()) {
            $resetCode->delete();
            return response()->json(['message' => 'Código expirado'], 400);
        }

        // Buscar el usuario
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        // Cerrar sesión en todos los dispositivos
        $user->tokens()->delete();

        // Eliminar el código usado
        $resetCode->delete();

        return response()->json(['message' => 'Contraseña actualizada exitosamente. Se ha cerrado sesión en todos los dispositivos.']);
    }
}