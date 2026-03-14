<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Valida que el usuario autenticado tenga la cuenta activa (is_active = true).
 * Aplicar despues de auth:sanctum en todas las rutas protegidas.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'No autenticado.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Tu cuenta esta desactivada. Contacta al administrador.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
