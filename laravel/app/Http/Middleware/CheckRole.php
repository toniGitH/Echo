<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\ValueObjects\UserRole;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Reconstruir User de dominio desde Eloquent
        $domainUser = User::fromPrimitives(
            $user->id,
            $user->name,
            $user->email,
            $user->roles ?? []
        );

        // Verificar si tiene alguno de los roles requeridos
        $requiredRoles = array_map(
            fn($role) => UserRole::fromString($role),
            $roles
        );

        if (!$domainUser->hasAnyRole($requiredRoles)) {
            return response()->json([
                'message' => 'Forbidden. Insufficient permissions.'
            ], 403);
        }

        return $next($request);
    }
}
