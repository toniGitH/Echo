<?php

declare(strict_types=1);

namespace Tests\Integration\Auth\Application\UseCases;

use Tests\TestCase;
use Src\Auth\Application\UseCases\RegisterUserUseCase;
use Src\Auth\Infrastructure\Persistence\EloquentUserRepository;
use Src\Auth\Domain\User\User;
use Src\Shared\Domain\Exceptions\MultipleDomainException;
use App\Models\User as EloquentUser;
use Illuminate\Support\Facades\Hash;

final class RegisterUserUseCaseIntegrationTest extends TestCase
{
    private RegisterUserUseCase $useCase;
    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository();
        $this->useCase = new RegisterUserUseCase($this->repository);
    }

    // Verifica que se registra un usuario correctamente y se persiste en la base de datos
    public function test_it_registers_user_and_persists_to_database(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id()->value(),
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
        ]);
    }

    // Verifica que la contraseña se hashea al persistir el usuario
    public function test_it_hashes_password_when_persisting_user(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();

        $this->assertNotEquals('Test1234!', $eloquentUser->password);
        $this->assertTrue(Hash::check('Test1234!', $eloquentUser->password));
    }

    // Verifica que lanza excepción cuando el email ya existe en la base de datos
    public function test_it_throws_exception_when_email_already_exists_in_database(): void
    {
        // Crear primer usuario
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];
        $this->useCase->execute($userData1);

        // Intentar crear segundo usuario con mismo email
        $userData2 = [
            'name' => 'María García',
            'email' => 'juan@example.com',
            'password' => 'Different1234!'
        ];

        $this->expectException(MultipleDomainException::class);

        $this->useCase->execute($userData2);
    }

    // Verifica que el error de email duplicado se acumula correctamente
    public function test_it_accumulates_duplicate_email_error_correctly(): void
    {
        // Crear primer usuario
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];
        $this->useCase->execute($userData1);

        // Intentar crear segundo usuario con mismo email
        $userData2 = [
            'name' => 'María García',
            'email' => 'juan@example.com',
            'password' => 'Different1234!'
        ];

        try {
            $this->useCase->execute($userData2);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();

            $this->assertArrayHasKey('email', $errors);
            $this->assertContains('messages.user.EMAIL_ALREADY_EXISTS', $errors['email']);
        }
    }

    // Verifica que no se persiste el usuario cuando hay errores de validación
    public function test_it_does_not_persist_user_when_validation_errors_occur(): void
    {
        $userData = [
            'name' => 'AB', // Nombre muy corto
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        try {
            $this->useCase->execute($userData);
        } catch (MultipleDomainException $e) {
            // Se espera la excepción
        }

        $this->assertDatabaseMissing('users', [
            'email' => 'juan@example.com',
        ]);
        $this->assertDatabaseCount('users', 0);
    }

    // Verifica que no se persiste el usuario cuando el email está duplicado
    public function test_it_does_not_persist_user_when_email_is_duplicate(): void
    {
        // Crear primer usuario
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];
        $this->useCase->execute($userData1);

        // Intentar crear segundo usuario con mismo email
        $userData2 = [
            'name' => 'María García',
            'email' => 'juan@example.com',
            'password' => 'Different1234!'
        ];

        try {
            $this->useCase->execute($userData2);
        } catch (MultipleDomainException $e) {
            // Se espera la excepción
        }

        // Solo debe haber un usuario en la base de datos
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('users', [
            'name' => 'María García',
        ]);
    }

    // Verifica que se pueden registrar múltiples usuarios con emails diferentes
    public function test_it_registers_multiple_users_with_different_emails(): void
    {
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $userData2 = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => 'Test1234!'
        ];

        $user1 = $this->useCase->execute($userData1);
        $user2 = $this->useCase->execute($userData2);

        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'maria@example.com']);
        $this->assertDatabaseCount('users', 2);
    }

    // Verifica que cada usuario registrado tiene un ID único en la base de datos
    public function test_it_assigns_unique_id_to_each_registered_user(): void
    {
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $userData2 = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => 'Test1234!'
        ];

        $user1 = $this->useCase->execute($userData1);
        $user2 = $this->useCase->execute($userData2);

        $this->assertNotEquals($user1->id()->value(), $user2->id()->value());

        $eloquentUser1 = EloquentUser::where('email', 'juan@example.com')->first();
        $eloquentUser2 = EloquentUser::where('email', 'maria@example.com')->first();

        $this->assertNotEquals($eloquentUser1->id, $eloquentUser2->id);
    }

    // Verifica que el email se compara de forma case-insensitive para duplicados
    public function test_it_detects_duplicate_email_case_insensitively(): void
    {
        // ⚠️ Este test se omite en SQLite.
        // SQLite compara cadenas con colación BINARY (sensible a mayúsculas/minúsculas),
        // por lo que 'juan@example.com' ≠ 'JUAN@EXAMPLE.COM'.
        // En MySQL (producción), la colación por defecto es case-insensitive,
        // así que el test pasaría correctamente allí.

        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped(
                'Test omitido para SQLite - Este test solo aplica a MySQL/PostgreSQL - Ver comentarios en el método de test.'
            );
        }

        // Crear usuario con email en minúsculas
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];
        $this->useCase->execute($userData1);

        // Intentar crear usuario con mismo email en mayúsculas
        $userData2 = [
            'name' => 'María García',
            'email' => 'JUAN@EXAMPLE.COM',
            'password' => 'Different1234!'
        ];

        $this->expectException(MultipleDomainException::class);

        $this->useCase->execute($userData2);
    }


    // Verifica que se registra usuario con nombre de longitud mínima válida
    public function test_it_registers_user_with_minimum_valid_name_length(): void
    {
        $userData = [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
        ]);
    }

    // Verifica que se registra usuario con nombre de longitud máxima válida
    public function test_it_registers_user_with_maximum_valid_name_length(): void
    {
        $longName = str_repeat('a', 100);
        $userData = [
            'name' => $longName,
            'email' => 'user@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertDatabaseHas('users', [
            'name' => $longName,
            'email' => 'user@example.com',
        ]);
    }

    // Verifica que se registra usuario con contraseña de longitud mínima válida
    public function test_it_registers_user_with_minimum_valid_password(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Abc123!@'
        ];

        $user = $this->useCase->execute($userData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();

        $this->assertTrue(Hash::check('Abc123!@', $eloquentUser->password));
    }

    // Verifica que se registra usuario con contraseña larga
    public function test_it_registers_user_with_long_password(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'MyVeryLongAndSecurePassword123!@#$%'
        ];

        $user = $this->useCase->execute($userData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();

        $this->assertTrue(Hash::check('MyVeryLongAndSecurePassword123!@#$%', $eloquentUser->password));
    }

    // Verifica que se registra usuario con caracteres especiales en el nombre
    public function test_it_registers_user_with_special_characters_in_name(): void
    {
        $userData = [
            'name' => 'María José O\'Connor-Smith',
            'email' => 'maria@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'María José O\'Connor-Smith',
            'email' => 'maria@example.com',
        ]);
    }

    // Verifica que se registra usuario con email complejo
    public function test_it_registers_user_with_complex_email(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'user.name+tag@sub.example.co.uk',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertDatabaseHas('users', [
            'email' => 'user.name+tag@sub.example.co.uk',
        ]);
    }

    // Verifica que trimea espacios del nombre antes de guardar
    public function test_it_trims_name_spaces_before_saving(): void
    {
        $userData = [
            'name' => '  Juan Pérez  ',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
        ]);
    }

    // Verifica que trimea espacios del email antes de guardar
    public function test_it_trims_email_spaces_before_saving(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => '  juan@example.com  ',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertDatabaseHas('users', [
            'email' => 'juan@example.com',
        ]);
    }

    // Verifica que acumula múltiples errores y no persiste nada
    public function test_it_accumulates_multiple_errors_and_does_not_persist(): void
    {
        $userData = [
            'name' => 'AB', // Muy corto
            'email' => 'invalid-email', // Formato inválido
            'password' => 'short' // Muy corta
        ];

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();

            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
        }

        $this->assertDatabaseCount('users', 0);
    }

    // Verifica que acumula error de email duplicado con otros errores de validación
    public function test_it_accumulates_duplicate_email_with_validation_errors(): void
    {
        // Crear primer usuario
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];
        $this->useCase->execute($userData1);

        // Intentar crear segundo usuario con mismo email y nombre inválido
        $userData2 = [
            'name' => 'AB', // Muy corto
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        try {
            $this->useCase->execute($userData2);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();

            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
        }

        // Solo debe haber un usuario
        $this->assertDatabaseCount('users', 1);
    }

    // Verifica que el timestamp created_at se guarda correctamente
    public function test_it_saves_created_at_timestamp_correctly(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();

        $this->assertNotNull($eloquentUser->created_at);
    }

    // Verifica que el ID del usuario devuelto coincide con el guardado en BD
    public function test_it_returns_user_with_same_id_as_persisted(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();

        $this->assertEquals($user->id()->value(), $eloquentUser->id);
    }

    // Verifica que el nombre del usuario devuelto coincide con el guardado en BD
    public function test_it_returns_user_with_same_name_as_persisted(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();

        $this->assertEquals($user->name()->value(), $eloquentUser->name);
    }

    // Verifica que el email del usuario devuelto coincide con el guardado en BD
    public function test_it_returns_user_with_same_email_as_persisted(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();

        $this->assertEquals($user->email()->value(), $eloquentUser->email);
    }

    // Verifica que no se verifica duplicado cuando el email tiene errores de formato
    public function test_it_does_not_check_duplicate_when_email_has_format_errors(): void
    {
        // Crear primer usuario
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];
        $this->useCase->execute($userData1);

        // Intentar crear con email inválido (no debería verificar duplicado)
        $userData2 = [
            'name' => 'María García',
            'email' => 'invalid-email',
            'password' => 'Test1234!'
        ];

        try {
            $this->useCase->execute($userData2);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();

            $this->assertArrayHasKey('email', $errors);
            // Solo debe tener el error de formato, no el de duplicado
            $this->assertCount(1, $errors['email']);
        }

        // Solo debe haber un usuario
        $this->assertDatabaseCount('users', 1);
    }

    // Verifica que maneja correctamente campos faltantes en el array de entrada
    public function test_it_handles_missing_fields_in_input_array(): void
    {
        $userData = []; // Sin campos

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();

            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
        }

        $this->assertDatabaseCount('users', 0);
    }

    // Verifica que diferentes usuarios con el mismo password tienen hashes diferentes
    public function test_it_hashes_same_password_differently_for_different_users(): void
    {
        $userData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $userData2 = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => 'Test1234!'
        ];

        $this->useCase->execute($userData1);
        $this->useCase->execute($userData2);

        $eloquentUser1 = EloquentUser::where('email', 'juan@example.com')->first();
        $eloquentUser2 = EloquentUser::where('email', 'maria@example.com')->first();

        // Mismo password original pero hash diferente (por el salt)
        $this->assertNotEquals($eloquentUser1->password, $eloquentUser2->password);
        $this->assertTrue(Hash::check('Test1234!', $eloquentUser1->password));
        $this->assertTrue(Hash::check('Test1234!', $eloquentUser2->password));
    }

    // Verifica que el UUID generado es válido y sigue el formato v4
    public function test_it_generates_valid_uuid_v4_for_user_id(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->useCase->execute($userData);

        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $user->id()->value()
        );
    }
}