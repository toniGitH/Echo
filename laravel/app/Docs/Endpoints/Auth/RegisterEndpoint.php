<?php

namespace App\Docs\Endpoints\Auth;

class RegisterEndpoint
{
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Auth"},
     *     summary="Registrar nuevo usuario",
     *     description="

## 📝 Registro de usuario

> Crea un nuevo usuario en el sistema con validaciones completas.

<details>
<summary><strong>✅ Validaciones aplicadas</strong></summary>

- **Nombre:** mínimo 3 caracteres, máximo 100

- **Email:** formato válido, único en el sistema

- **Contraseña:** mínimo 8 caracteres, máximo 50, debe incluir:
  - Al menos una letra mayúscula
  - Al menos una letra minúscula
  - Al menos un número
  - Al menos un carácter especial (!@#$%^&*()_-+=[]{}|;:'\"",.<>/?¿)

- **Confirmación de contraseña:** debe coincidir con la contraseña

- **Roles:** mínimo 1 rol, máximo 2 roles
  - Roles permitidos: `client`, `follower`
  - ⚠️ El rol `admin` NO está permitido en registro público

</details>

<details>
<summary><strong>🌐 Nota sobre idiomas</strong></summary>

> Esta documentación está en español, pero los mensajes JSON de la API están en inglés.

> Si quieres cambiar el idioma de las respuestas de la API, modifica la variable `APP_LOCALE` en tu archivo `laravel/.env`:

- `APP_LOCALE=en` para inglés
- `APP_LOCALE=es` para español

</details>",
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation", "roles"},
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
     * - Al menos un carácter especial (!@#$%^&*()_-+=[]{}|;:',.<>/?¿)",
     *                 example="Password123!"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 description="Confirmación de la contraseña (debe coincidir exactamente con password)",
     *                 example="Password123!"
     *             ),
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *                 description="Roles del usuario. Puedes seleccionar uno o ambos roles.
     * 
     * **Roles disponibles en registro público:**
     * - `client`: Usuario que puede crear y gestionar eventos
     * - `follower`: Usuario que puede seguir eventos
     * 
     * ⚠️ **Nota importante:** El rol `admin` NO está permitido en el registro público. Los administradores solo pueden ser designados por clientes mediante endpoints específicos (no disponibles en esta fase).",
     *                 minItems=1,
     *                 maxItems=2,
     *                 @OA\Items(
     *                     type="string",
     *                     enum={"client", "follower"},
     *                     example="client"
     *                 ),
     *                 example={"client", "follower"}
     *             ),
     *             @OA\Examples(example="usuario_cliente", summary="Usuario solo como cliente", value={"name": "Juan Pérez", "email": "juan.perez@example.com", "password": "Password123!", "password_confirmation": "Password123!", "roles": {"client"}}),
     *             @OA\Examples(example="usuario_follower", summary="Usuario solo como follower", value={"name": "María García", "email": "maria.garcia@example.com", "password": "SecurePass456!", "password_confirmation": "SecurePass456!", "roles": {"follower"}}),
     *             @OA\Examples(example="usuario_dual", summary="Usuario con ambos roles (cliente y follower)", value={"name": "Pedro López", "email": "pedro.lopez@example.com", "password": "MyP@ssw0rd789", "password_confirmation": "MyP@ssw0rd789", "roles": {"client", "follower"}})
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
     *                 ),
     *                 @OA\Property(
     *                     property="roles",
     *                     type="array",
     *                     description="Roles asignados al usuario",
     *                     @OA\Items(
     *                         type="string",
     *                         enum={"client", "follower"}
     *                     ),
     *                     example={"client", "follower"}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - JSON malformado o sintaxis inválida",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The request could not be understood due to malformed syntax."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Method Not Allowed - Método HTTP incorrecto (debe ser POST)",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The GET method is not supported for this route. Supported methods: POST."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=415,
     *         description="Unsupported Media Type - Falta header Content-Type: application/json",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The request entity has a media type which the server or resource does not support."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity - Error de validación en los datos enviados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Examples(example="nombre_vacio", summary="Nombre vacío", value={"message": "The given data was invalid.", "errors": {"name": {"The name field is required."}}}),
     *             @OA\Examples(example="nombre_corto", summary="Nombre demasiado corto", value={"message": "The given data was invalid.", "errors": {"name": {"The name must be at least 3 characters."}}}),
     *             @OA\Examples(example="nombre_largo", summary="Nombre demasiado largo", value={"message": "The given data was invalid.", "errors": {"name": {"The name must not be greater than 100 characters."}}}),
     *             @OA\Examples(example="email_vacio", summary="Email vacío", value={"message": "The given data was invalid.", "errors": {"email": {"The email field is required."}}}),
     *             @OA\Examples(example="email_invalido", summary="Email con formato inválido", value={"message": "The given data was invalid.", "errors": {"email": {"The email must be a valid email address."}}}),
     *             @OA\Examples(example="email_duplicado", summary="Email ya registrado", value={"message": "The given data was invalid.", "errors": {"email": {"The email has already been taken."}}}),
     *             @OA\Examples(example="password_vacio", summary="Contraseña vacía", value={"message": "The given data was invalid.", "errors": {"password": {"The password field is required."}}}),
     *             @OA\Examples(example="password_corto", summary="Contraseña demasiado corta", value={"message": "The given data was invalid.", "errors": {"password": {"The password must be at least 8 characters."}}}),
     *             @OA\Examples(example="password_debil", summary="Contraseña sin requisitos de seguridad", value={"message": "The given data was invalid.", "errors": {"password": {"The password must contain at least one uppercase letter, one lowercase letter, one number and one special character."}}}),
     *             @OA\Examples(example="password_sin_confirmar", summary="Contraseña no confirmada", value={"message": "The given data was invalid.", "errors": {"password": {"The password confirmation does not match."}}}),
     *             @OA\Examples(example="roles_vacio", summary="Sin roles", value={"message": "The given data was invalid.", "errors": {"roles": {"The roles field is required."}}}),
     *             @OA\Examples(example="roles_array_vacio", summary="Array de roles vacío", value={"message": "The given data was invalid.", "errors": {"roles": {"The roles must have at least 1 item."}}}),
     *             @OA\Examples(example="rol_invalido", summary="Rol admin no permitido", value={"message": "The given data was invalid.", "errors": {"roles.0": {"The selected roles.0 is invalid."}}}),
     *             @OA\Examples(example="errores_multiples", summary="Múltiples errores de validación", value={"message": "The given data was invalid.", "errors": {"name": {"The name must be at least 3 characters."}, "email": {"The email has already been taken."}, "password": {"The password must be at least 8 characters.", "The password must contain at least one uppercase letter, one lowercase letter, one number and one special character."}, "roles": {"The roles field is required."}}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error - Error inesperado del servidor",
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
