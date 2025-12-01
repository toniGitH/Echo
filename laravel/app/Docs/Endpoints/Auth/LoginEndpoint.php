<?php

namespace App\Docs\Endpoints\Auth;

class LoginEndpoint
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Iniciar sesión (Bearer Token)",
     *     description="Autentica a un usuario existente y devuelve un token de acceso Bearer.
     *
     * **🔐 Autenticación por tokens (API Mode):**
     *
     * Este endpoint usa autenticación **stateless** con tokens Bearer (Laravel Sanctum API tokens).
     *
     * **Flujo de autenticación:**
     *
     * 1. **Hacer login** (este endpoint):
     *    ```
     *    POST http://localhost:8988/api/auth/login
     *    ```
     *    Recibes un token de acceso en la respuesta.
     *
     * 2. **Usar el token** en peticiones posteriores:
     *    ```
     *    Authorization: Bearer {token}
     *    ```
     *
     * **Ventajas de autenticación por tokens:**
     * - ✅ Funciona perfectamente en Postman, Insomnia, cURL
     * - ✅ Ideal para aplicaciones móviles y APIs públicas
     * - ✅ No requiere cookies ni CSRF tokens
     * - ✅ Stateless (no depende de sesiones del servidor)
     * - ✅ Frontend y backend pueden estar en dominios diferentes
     *
     * **Para probar este endpoint:**
     * - Usa Postman, Insomnia, cURL o cualquier cliente HTTP
     * - Copia el token de la respuesta
     * - Úsalo en el header `Authorization: Bearer {token}` para peticiones autenticadas
     *
     * **Validaciones aplicadas:**
     * - Email: formato válido, debe existir en el sistema
     * - Contraseña: debe coincidir con la almacenada",
     *     operationId="loginUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="Email del usuario registrado",
     *                 example="mortadelo@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="Contraseña del usuario",
     *                 example="SecurePass123!"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso - Token de acceso generado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje de confirmación del login",
     *                 example="Login successful."
     *             ),
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 description="Token de acceso Bearer para autenticar peticiones posteriores",
     *                 example="1|a8K3mN9pQ2rT5vW8xY1zA4bC6dE9fG2hJ5kL8mN0pQ3rT6vW9xY2zA5bC8dE1fG4"
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Datos del usuario autenticado",
     *                 @OA\Property(
     *                     property="id",
     *                     type="string",
     *                     format="uuid",
     *                     description="ID único del usuario (UUID v4)",
     *                     example="974561d8-9ca9-4742-9efb-de27deab3b26"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Nombre del usuario",
     *                     example="Mortadelo"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="Email del usuario",
     *                     example="mortadelo@example.com"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas - Email o contraseña incorrectos",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje de error de autenticación",
     *                 example="Invalid credentials."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación - Datos inválidos o incompletos",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje general del error de validación"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Objeto con los errores de cada campo",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke()
    {
    }
}
