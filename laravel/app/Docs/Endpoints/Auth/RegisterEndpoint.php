<?php

namespace App\Docs\Endpoints\Auth;

class RegisterEndpoint
{
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Auth"},
     *     summary="Registrar nuevo usuario",
     *     description="Crea un nuevo usuario en el sistema con validaciones completas.
     *
     * **Validaciones aplicadas:**
     * - Nombre: mínimo 3 caracteres, máximo 100
     * - Email: formato válido, único en el sistema
     * - Contraseña: mínimo 8 caracteres, máximo 50, debe incluir mayúscula, minúscula, número y carácter especial
     * - Confirmación de contraseña: debe coincidir con la contraseña
     *
     * **Nota sobre idiomas:**
     * - Esta documentación está en español, pero los mensajes JSON de la API están en inglés.
     * - Si quieres cambiar el idioma de las respuestas de la API, modifica la variable `APP_LOCALE` en tu archivo `.env`:
     *   - `APP_LOCALE=en` para inglés
     *   - `APP_LOCALE=es` para español",
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 minLength=3,
     *                 maxLength=100,
     *                 description="Nombre completo del usuario",
     *                 example="Juan Pérez"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 maxLength=255,
     *                 description="Correo electrónico único del usuario",
     *                 example="juan.perez@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 minLength=8,
     *                 maxLength=50,
     *                 description="Contraseña del usuario. Debe cumplir con los siguientes requisitos:
     * - Mínimo 8 caracteres
     * - Máximo 50 caracteres
     * - Al menos una letra mayúscula
     * - Al menos una letra minúscula
     * - Al menos un número
     * - Al menos un carácter especial (!@#$%^&*()_-+=[]{}|;:'"",.<>/?¿)",
     *                 example="Password123!"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 description="Confirmación de la contraseña (debe coincidir exactamente con password)",
     *                 example="Password123!"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje de confirmación del registro",
     *                 example="User registered successfully."
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Datos del usuario registrado",
     *                 @OA\Property(
     *                     property="id",
     *                     type="string",
     *                     format="uuid",
     *                     description="ID único del usuario (UUID v4)",
     *                     example="9d4e8c1a-3b2f-4d5e-8f9a-1b2c3d4e5f6a"
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Mensaje descriptivo del error interno",
     *                 example="An unexpected error occurred. Please try again later."
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke()
    {
    }
}
