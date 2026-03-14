<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifica que el usuario autenticado tenga AL MENOS UNO de los roles indicados.
 * Uso en rutas: ->middleware('role:admin')  o  ->middleware('role:admin,manager')
 *
 * Diferencia con CheckRole: este middleware ademas valida que la cuenta este activa
 * antes de comprobar el rol.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Sesion activa
        if (! $user) {
            return response()->json([
                'message' => 'No autenticado.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // 2. Cuenta activa
        if (! $user->is_active) {
            return response()->json([
                'message' => 'Tu cuenta esta desactivada. Contacta al administrador.',
            ], Response::HTTP_FORBIDDEN);
        }

        // 3. Verificacion de email
        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Debes verificar tu correo electronico.',
            ], Response::HTTP_FORBIDDEN);
        }

        // 4. Rol requerido
        if (! empty($roles) && ! $user->hasAnyRole($roles)) {
            return response()->json([
                'message' => 'No tienes permiso. Roles requeridos: ' . implode(', ', $roles),
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
