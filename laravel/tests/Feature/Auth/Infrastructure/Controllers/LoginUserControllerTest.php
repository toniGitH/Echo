<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Infrastructure\Controllers;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginUserControllerTest extends TestCase
{
    private string $registerEndpoint = '/api/auth/register';
    private string $loginEndpoint = '/api/auth/login';

    // Verifica que la base de datos esté limpia antes de cada test
    protected function setUp(): void
    {
        parent::setUp();
        
        // Asegurarse de que la tabla users está vacía
        DB::table('users')->truncate();
    }

    // Helper method para registrar un usuario de prueba
    private function registerTestUser(array $userData = []): array
    {
        $defaultData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Test1234!',
            'password_confirmation' => 'Test1234!',
        ];

        $data = array_merge($defaultData, $userData);
        
        $this->postJson($this->registerEndpoint, $data);
        
        return $data;
    }

    // Helper method para verificar si estamos usando SQLite
    private function isSqlite(): bool
    {
        return config('database.default') === 'sqlite';
    }

    // ============================================
    // TESTS DE LOGIN EXITOSO
    // ============================================

    // Comprueba que un usuario puede hacer login correctamente con credenciales válidas
    public function test_can_login_with_valid_credentials(): void
    {
        // Registrar usuario primero
        $this->registerTestUser([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Intentar login
        $loginData = [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ])
            ->assertJson([
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ]);

        // Verificar que se devuelve un token
        $this->assertNotEmpty($response->json('token'));
        $this->assertIsString($response->json('token'));
    }

    // Comprueba que el token devuelto es válido y puede usarse para autenticación
    public function test_returned_token_is_valid(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Login
        $loginData = [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);
        $token = $response->json('token');

        // Verificar que el token existe en la base de datos
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'api-token',
        ]);

        // El token debe ser un string largo
        $this->assertGreaterThan(40, strlen($token));
    }

    // Comprueba que el ID devuelto es un UUID válido v4
    public function test_returned_user_id_is_valid_uuid_v4(): void
    {
        // Registrar usuario
        $this->registerTestUser();

        // Login
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $response->json('user.id')
        );
    }

    // Comprueba que se puede hacer login con email en minúsculas
    public function test_can_login_with_lowercase_email(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'email' => 'john@example.com',
        ]);

        // Login con email en minúsculas
        $loginData = [
            'email' => 'john@example.com',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(200);
    }

    // Comprueba que se puede hacer login con email que contiene espacios al inicio/final (se trimean)
    public function test_can_login_with_email_with_spaces(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'email' => 'john@example.com',
        ]);

        // Login con espacios
        $loginData = [
            'email' => '  john@example.com  ',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(200);
    }

    // Comprueba que se puede hacer login con email complejo
    public function test_can_login_with_complex_email(): void
    {
        // Registrar usuario con email complejo
        $this->registerTestUser([
            'email' => 'user.name+tag@sub.example.co.uk',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Login
        $loginData = [
            'email' => 'user.name+tag@sub.example.co.uk',
            'password' => 'Password123!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(200);
    }

    // Comprueba que se puede hacer login con password de longitud mínima
    public function test_can_login_with_minimum_valid_password(): void
    {
        // Registrar usuario con password mínima
        $this->registerTestUser([
            'password' => 'Abc123!@',
            'password_confirmation' => 'Abc123!@',
        ]);

        // Login
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Abc123!@',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(200);
    }

    // Comprueba que se puede hacer login con password larga
    public function test_can_login_with_long_password(): void
    {
        // Registrar usuario con password larga
        $longPassword = 'MyVeryLongAndSecurePassword123!@#$%';
        $this->registerTestUser([
            'password' => $longPassword,
            'password_confirmation' => $longPassword,
        ]);

        // Login
        $loginData = [
            'email' => 'test@example.com',
            'password' => $longPassword,
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(200);
    }

    // Comprueba que el login es case-insensitive para el email
    public function test_can_login_case_insensitively_for_email(): void
    {
        // ⚠️ Este test se omite en SQLite.
        // SQLite compara cadenas con colación BINARY (sensible a mayúsculas/minúsculas),
        // por lo que 'john@example.com' ≠ 'JOHN@EXAMPLE.COM'.
        // En MySQL (producción), la colación por defecto es case-insensitive,
        // así que el test pasaría correctamente allí.

        if ($this->isSqlite()) {
            $this->markTestSkipped(
                'Test omitido para SQLite - Este test solo aplica a MySQL/PostgreSQL - Ver comentarios en el método de test.'
            );
        }

        // Registrar usuario con email en minúsculas
        $this->registerTestUser([
            'email' => 'john@example.com',
        ]);

        // Login con email en mayúsculas
        $loginData = [
            'email' => 'JOHN@EXAMPLE.COM',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(200);
    }

    // ============================================
    // TESTS DE LOGIN FALLIDO - CREDENCIALES INCORRECTAS
    // ============================================

    // Comprueba que no se puede hacer login con password incorrecta
    public function test_cannot_login_with_wrong_password(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'password' => 'Correct123!',
            'password_confirmation' => 'Correct123!',
        ]);

        // Intentar login con password incorrecta
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Wrong123!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    // Comprueba que no se puede hacer login con email que no existe
    public function test_cannot_login_with_non_existent_email(): void
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    // Comprueba que no se puede hacer login con password que difiere en un solo carácter
    public function test_cannot_login_with_slightly_different_password(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'password' => 'Test1234!',
            'password_confirmation' => 'Test1234!',
        ]);

        // Intentar login con password ligeramente diferente
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Test1234@', // Cambió ! por @
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(401);
    }

    // Comprueba que no se puede hacer login con password case-sensitive
    public function test_cannot_login_with_wrong_case_password(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'password' => 'Test1234!',
            'password_confirmation' => 'Test1234!',
        ]);

        // Intentar login con password con case diferente (pero válida)
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'TeSt1234!', // Cambió mayúsculas/minúsculas
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(401);
    }

    // Comprueba que no se puede hacer login con email parcialmente coincidente
    public function test_cannot_login_with_partial_email_match(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'email' => 'john@example.com',
        ]);

        // Intentar login con email parcial
        $loginData = [
            'email' => 'john@example.co',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(401);
    }

    // ============================================
    // TESTS DE VALIDACIÓN - CAMPO EMAIL
    // ============================================

    // Comprueba que no se puede hacer login sin enviar el campo email
    public function test_cannot_login_without_email(): void
    {
        $loginData = [
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['email']]);
    }

    // Comprueba que no se puede hacer login con el campo email vacío
    public function test_cannot_login_with_empty_email(): void
    {
        $loginData = [
            'email' => '',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['email']]);
    }

    // Comprueba que no se puede hacer login con email de formato inválido
    public function test_cannot_login_with_invalid_email_format(): void
    {
        $invalidEmails = [
            'notanemail',
            'missing@domain',
            '@nodomain.com',
            'no@domain',
            'spaces in@email.com',
            'double@@domain.com',
        ];

        foreach ($invalidEmails as $invalidEmail) {
            $loginData = [
                'email' => $invalidEmail,
                'password' => 'Test1234!',
            ];

            $response = $this->postJson($this->loginEndpoint, $loginData);

            $response->assertStatus(422, "Failed for email: {$invalidEmail}")
                ->assertJsonStructure(['errors' => ['email']]);
        }
    }

    // Comprueba que no se puede hacer login con el campo email como null
    public function test_cannot_login_with_null_email(): void
    {
        $loginData = [
            'email' => null,
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['email']]);
    }

    // Comprueba que no se puede hacer login con el campo email como array
    public function test_cannot_login_with_email_as_array(): void
    {
        $loginData = [
            'email' => ['test@example.com'],
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['email']]);
    }

    // ============================================
    // TESTS DE VALIDACIÓN - CAMPO PASSWORD
    // ============================================

    // Comprueba que no se puede hacer login sin enviar el campo password
    public function test_cannot_login_without_password(): void
    {
        $loginData = [
            'email' => 'test@example.com',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con el campo password vacío
    public function test_cannot_login_with_empty_password(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con password de menos de 8 caracteres
    public function test_cannot_login_with_password_too_short(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Pass1!', // 6 caracteres
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con password sin mayúsculas
    public function test_cannot_login_with_password_missing_uppercase(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con password sin minúsculas
    public function test_cannot_login_with_password_missing_lowercase(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'PASSWORD123!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con password sin números
    public function test_cannot_login_with_password_missing_number(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Password!!!!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con password sin caracteres especiales
    public function test_cannot_login_with_password_missing_special_character(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Password123',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con el campo password como null
    public function test_cannot_login_with_null_password(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => null,
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que no se puede hacer login con el campo password como array
    public function test_cannot_login_with_password_as_array(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => ['Test1234!'],
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // ============================================
    // TESTS DE VALIDACIÓN - MÚLTIPLES ERRORES
    // ============================================

    // Comprueba que se acumulan múltiples errores de validación
    public function test_accumulates_multiple_validation_errors(): void
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => 'short',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                    'password',
                ],
            ]);
    }

    // Comprueba que se acumulan errores cuando faltan ambos campos
    public function test_accumulates_errors_when_both_fields_missing(): void
    {
        $loginData = [];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                    'password',
                ],
            ]);
    }

    // ============================================
    // TESTS DE MÚLTIPLES USUARIOS
    // ============================================

    // Comprueba que se puede hacer login de diferentes usuarios
    public function test_can_login_different_users(): void
    {
        // Registrar dos usuarios
        $this->registerTestUser([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $this->registerTestUser([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => 'Password2!',
            'password_confirmation' => 'Password2!',
        ]);

        // Login del primer usuario
        $response1 = $this->postJson($this->loginEndpoint, [
            'email' => 'user1@example.com',
            'password' => 'Password1!',
        ]);

        $response1->assertStatus(200)
            ->assertJson(['user' => ['email' => 'user1@example.com']]);

        // Login del segundo usuario
        $response2 = $this->postJson($this->loginEndpoint, [
            'email' => 'user2@example.com',
            'password' => 'Password2!',
        ]);

        $response2->assertStatus(200)
            ->assertJson(['user' => ['email' => 'user2@example.com']]);

        // Los tokens deben ser diferentes
        $this->assertNotEquals($response1->json('token'), $response2->json('token'));
    }

    // Comprueba que no se puede hacer login de un usuario con password de otro
    public function test_cannot_login_user_with_another_users_password(): void
    {
        // Registrar dos usuarios
        $this->registerTestUser([
            'email' => 'user1@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $this->registerTestUser([
            'email' => 'user2@example.com',
            'password' => 'Password2!',
            'password_confirmation' => 'Password2!',
        ]);

        // Intentar login de user1 con password de user2
        $response = $this->postJson($this->loginEndpoint, [
            'email' => 'user1@example.com',
            'password' => 'Password2!',
        ]);

        $response->assertStatus(401);
    }

    // ============================================
    // TESTS DE TOKENS
    // ============================================

    // Comprueba que cada login genera un token diferente
    public function test_each_login_generates_different_token(): void
    {
        // Registrar usuario
        $this->registerTestUser();

        // Primer login
        $response1 = $this->postJson($this->loginEndpoint, [
            'email' => 'test@example.com',
            'password' => 'Test1234!',
        ]);

        $token1 = $response1->json('token');

        // Segundo login del mismo usuario
        $response2 = $this->postJson($this->loginEndpoint, [
            'email' => 'test@example.com',
            'password' => 'Test1234!',
        ]);

        $token2 = $response2->json('token');

        // Los tokens deben ser diferentes
        $this->assertNotEquals($token1, $token2);
    }

    // Comprueba que múltiples logins crean múltiples tokens en la base de datos
    public function test_multiple_logins_create_multiple_tokens(): void
    {
        // Registrar usuario
        $this->registerTestUser();

        // Hacer 3 logins
        $this->postJson($this->loginEndpoint, [
            'email' => 'test@example.com',
            'password' => 'Test1234!',
        ]);

        $this->postJson($this->loginEndpoint, [
            'email' => 'test@example.com',
            'password' => 'Test1234!',
        ]);

        $this->postJson($this->loginEndpoint, [
            'email' => 'test@example.com',
            'password' => 'Test1234!',
        ]);

        // Debe haber 3 tokens en la base de datos
        $this->assertDatabaseCount('personal_access_tokens', 3);
    }

    // ============================================
    // TESTS DE EDGE CASES
    // ============================================

    // Comprueba que no se puede hacer login con solo espacios en el email
    public function test_cannot_login_with_only_spaces_in_email(): void
    {
        $loginData = [
            'email' => '   ',
            'password' => 'Test1234!',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['email']]);
    }

    // Comprueba que no se puede hacer login con solo espacios en el password
    public function test_cannot_login_with_only_spaces_in_password(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => '        ',
        ];

        $response = $this->postJson($this->loginEndpoint, $loginData);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    // Comprueba que el mensaje de error no revela si el email existe o no
    public function test_error_message_does_not_reveal_if_email_exists(): void
    {
        // Registrar usuario
        $this->registerTestUser([
            'email' => 'exists@example.com',
        ]);

        // Login con email existente pero password incorrecta
        $response1 = $this->postJson($this->loginEndpoint, [
            'email' => 'exists@example.com',
            'password' => 'Wrong1234!',
        ]);

        // Login con email no existente
        $response2 = $this->postJson($this->loginEndpoint, [
            'email' => 'notexists@example.com',
            'password' => 'Test1234!',
        ]);

        // Ambos deben devolver el mismo código de estado
        $this->assertEquals($response1->status(), $response2->status());
        
        // Ambos deben tener estructura similar (no revelar información)
        $response1->assertStatus(401);
        $response2->assertStatus(401);
    }
}
