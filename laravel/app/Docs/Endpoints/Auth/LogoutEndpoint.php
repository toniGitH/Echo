<?php

namespace App\Docs\Endpoints\Auth;

class LogoutEndpoint
{
    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Cerrar sesión (Logout)",
     *     description="

## 🚪 Cerrar sesión

> Revoca el token de acceso actual del usuario autenticado.

> ⚠️ **Autenticación requerida:** Este endpoint requiere autenticación mediante Bearer token.

<details>
<summary><strong>🔐 ¿Qué hace este endpoint?</strong></summary>

- Revoca (elimina) el token de acceso actual del usuario
- El token queda inválido inmediatamente
- No se pueden revocar tokens de otros usuarios, solo el token actual

</details>

<details>
<summary><strong>⚡ Después del logout</strong></summary>

- El token ya no funcionará para futuras peticiones
- Recibirás error 401 (Unauthenticated) si intentas usar el token revocado
- Para volver a autenticarte, debes hacer login nuevamente

</details>

<details>
<summary><strong>💡 Casos de uso</strong></summary>

- Cerrar sesión en la aplicación
- Invalidar el token antes de generar uno nuevo
- Seguridad: revocar acceso si el token fue comprometido

</details>",
     *     operationId="logoutUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout exitoso - Token revocado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje de confirmación del logout",
     *                 example="Logged out successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado - Token inválido, expirado o no proporcionado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje de error de autenticación",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke()
    {
    }
}
