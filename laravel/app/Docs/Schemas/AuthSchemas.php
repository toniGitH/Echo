<?php

namespace App\Docs\Schemas;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     title="Petición de Login",
 *     description="Datos necesarios para iniciar sesión",
 *     required={"email", "password"},
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Correo electrónico del usuario",
 *         example="antonio@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="Contraseña del usuario",
 *         example="password123"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     title="Petición de Registro",
 *     description="Datos necesarios para registrar un nuevo usuario",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del usuario",
 *         example="Antonio"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Correo electrónico del usuario",
 *         example="antonio@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="Contraseña del usuario",
 *         example="password123"
 *     ),
 *     @OA\Property(
 *         property="password_confirmation",
 *         type="string",
 *         format="password",
 *         description="Confirmación de la contraseña",
 *         example="password123"
 *     ),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         description="Roles del usuario",
 *         @OA\Items(
 *             type="string",
 *             example="client"
 *         )
 *     )
 * )
 */
class AuthSchemas
{
}
