<?php

namespace App\Docs\Endpoints\Auth;

class LoginEndpoint
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Iniciar sesión (Bearer Token)",
     *     description="

## 🔐 Autenticación por tokens

> Este endpoint usa autenticación **stateless** con tokens Bearer (Laravel Sanctum API tokens).

> Autentica a un usuario existente y devuelve un token de acceso Bearer.

<details>
<summary><strong>🔐 Flujo de autenticación</strong></summary>

#### 1️⃣ Hacer login (este endpoint)

```
POST http://localhost:8988/api/auth/login
```

> Recibes un token de acceso en la respuesta.

#### 2️⃣ Usar el token en peticiones posteriores

```
Authorization: Bearer {token}
```

</details>

<details>
<summary><strong>✨ Ventajas de autenticación por tokens</strong></summary>

- Funciona perfectamente en Postman, Insomnia, cURL y Swagger UI
- Ideal para aplicaciones móviles y APIs públicas
- No requiere cookies ni CSRF tokens
- Stateless (no depende de sesiones del servidor)
- Frontend y backend pueden estar en dominios diferentes

</details>

<details>
<summary><strong>✅ Validaciones aplicadas</strong></summary>

- **Email:** formato válido, debe existir en el sistema
- **Contraseña:** debe coincidir con la almacenada

</details>",
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
     *             ),
     *             @OA\Examples(example="usuario_cliente", summary="Login como cliente", value={"email": "juan.perez@example.com", "password": "Password123!"}),
     *             @OA\Examples(example="usuario_follower", summary="Login como follower", value={"email": "maria.garcia@example.com", "password": "SecurePass456!"})
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
     *         description="Unprocessable Entity - Error de validación en los datos enviados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Examples(example="email_vacio", summary="Email vacío", value={"message": "The given data was invalid.", "errors": {"email": {"The email field is required."}}}),
     *             @OA\Examples(example="email_invalido", summary="Email con formato inválido", value={"message": "The given data was invalid.", "errors": {"email": {"The email must be a valid email address."}}}),
     *             @OA\Examples(example="password_vacio", summary="Contraseña vacía", value={"message": "The given data was invalid.", "errors": {"password": {"The password field is required."}}}),
     *             @OA\Examples(example="password_corto", summary="Contraseña demasiado corta", value={"message": "The given data was invalid.", "errors": {"password": {"The password must be at least 8 characters."}}}),
     *             @OA\Examples(example="password_debil", summary="Contraseña sin requisitos de seguridad", value={"message": "The given data was invalid.", "errors": {"password": {"The password must contain at least one uppercase letter, one lowercase letter, one number and one special character."}}}),
     *             @OA\Examples(example="errores_multiples", summary="Múltiples errores de validación", value={"message": "The given data was invalid.", "errors": {"email": {"The email must be a valid email address."}, "password": {"The password must be at least 8 characters.", "The password must contain at least one uppercase letter, one lowercase letter, one number and one special character."}}})
     *         )
     *     )
     * )
     */
    public function __invoke()
    {
    }
}
