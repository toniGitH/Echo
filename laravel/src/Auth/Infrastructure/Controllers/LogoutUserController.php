<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controlador para cerrar la sesión del usuario (Logout).
 * Revoca el token de acceso actual del usuario autenticado.
 */
final class LogoutUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Revocar el token actual del usuario autenticado
        // Esto elimina el token de la base de datos (tabla personal_access_tokens)
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse(['message' => 'Logged out successfully.'], 200);
    }
}
