<?php

declare(strict_types=1);

namespace Tests\Integration\Auth\Application\UseCases;

use Tests\TestCase;
use Src\Auth\Application\UseCases\LoginUserUseCase;
use Src\Auth\Application\UseCases\RegisterUserUseCase;
use Src\Auth\Infrastructure\Persistence\EloquentUserRepository;
use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\Exceptions\InvalidCredentialsException;
use Src\Shared\Domain\Exceptions\MultipleDomainException;
use App\Models\User as EloquentUser;

final class LoginUserUseCaseIntegrationTest extends TestCase
{
    private LoginUserUseCase $loginUseCase;
    private RegisterUserUseCase $registerUseCase;
    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository();
        $this->loginUseCase = new LoginUserUseCase($this->repository);
        $this->registerUseCase = new RegisterUserUseCase($this->repository);
    }

    // Verifica que se puede hacer login con credenciales válidas
    public function test_it_logs_in_user_with_valid_credentials(): void
    {
        // Registrar usuario primero
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Hacer login
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('juan@example.com', $user->email()->value());
        $this->assertEquals('Juan Pérez', $user->name()->value());
    }

    // Verifica que el usuario devuelto no tiene password
    public function test_it_returns_user_without_password(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Hacer login
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $this->assertNull($user->password());
    }

    // Verifica que lanza excepción con password incorrecta
    public function test_it_throws_exception_with_invalid_password(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Intentar login con password incorrecta
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Wrong1234!'
        ];

        $this->expectException(InvalidCredentialsException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que lanza excepción cuando el usuario no existe
    public function test_it_throws_exception_when_user_does_not_exist(): void
    {
        $loginData = [
            'email' => 'noexiste@example.com',
            'password' => 'Test1234!'
        ];

        $this->expectException(InvalidCredentialsException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que lanza excepción cuando el email está vacío
    public function test_it_throws_exception_when_email_is_empty(): void
    {
        $loginData = [
            'email' => '',
            'password' => 'Test1234!'
        ];

        $this->expectException(MultipleDomainException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que lanza excepción cuando el email es inválido
    public function test_it_throws_exception_when_email_is_invalid(): void
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => 'Test1234!'
        ];

        $this->expectException(MultipleDomainException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que lanza excepción cuando la password está vacía
    public function test_it_throws_exception_when_password_is_empty(): void
    {
        $loginData = [
            'email' => 'juan@example.com',
            'password' => ''
        ];

        $this->expectException(MultipleDomainException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que lanza excepción cuando la password no cumple requisitos
    public function test_it_throws_exception_when_password_is_invalid(): void
    {
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'short'
        ];

        $this->expectException(MultipleDomainException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que acumula múltiples errores de validación
    public function test_it_accumulates_multiple_validation_errors(): void
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => 'short'
        ];

        try {
            $this->loginUseCase->execute($loginData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();

            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que trimea espacios del email antes de verificar
    public function test_it_trims_email_spaces_before_checking(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Login con espacios en el email
        $loginData = [
            'email' => '  juan@example.com  ',
            'password' => 'Test1234!'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('juan@example.com', $user->email()->value());
    }

    // Verifica que se puede hacer login con email complejo
    public function test_it_logs_in_with_complex_email(): void
    {
        // Registrar usuario con email complejo
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'user.name+tag@sub.example.co.uk',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Login
        $loginData = [
            'email' => 'user.name+tag@sub.example.co.uk',
            'password' => 'Test1234!'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('user.name+tag@sub.example.co.uk', $user->email()->value());
    }

    // Verifica que el login es case-insensitive para el email
    public function test_it_logs_in_case_insensitively_for_email(): void
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

        // Registrar usuario con email en minúsculas
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Login con email en mayúsculas
        $loginData = [
            'email' => 'JUAN@EXAMPLE.COM',
            'password' => 'Test1234!'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $this->assertInstanceOf(User::class, $user);
    }

    // Verifica que no hace login con email parcialmente coincidente
    public function test_it_does_not_login_with_partial_email_match(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Intentar login con email parcial
        $loginData = [
            'email' => 'juan@example.co',
            'password' => 'Test1234!'
        ];

        $this->expectException(InvalidCredentialsException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que maneja campos faltantes en el array de credenciales
    public function test_it_handles_missing_fields_in_credentials(): void
    {
        $loginData = []; // Sin campos

        try {
            $this->loginUseCase->execute($loginData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();

            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que el ID devuelto coincide con el de la base de datos
    public function test_it_returns_user_with_correct_id_from_database(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $registeredUser = $this->registerUseCase->execute($registerData);

        // Login
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $loggedInUser = $this->loginUseCase->execute($loginData);

        $this->assertEquals($registeredUser->id()->value(), $loggedInUser->id()->value());

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();
        $this->assertEquals($eloquentUser->id, $loggedInUser->id()->value());
    }

    // Verifica que el email devuelto coincide con el de la base de datos
    public function test_it_returns_user_with_correct_email_from_database(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Login
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();
        $this->assertEquals($eloquentUser->email, $user->email()->value());
    }

    // Verifica que el nombre devuelto coincide con el de la base de datos
    public function test_it_returns_user_with_correct_name_from_database(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Login
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $eloquentUser = EloquentUser::where('email', 'juan@example.com')->first();
        $this->assertEquals($eloquentUser->name, $user->name()->value());
    }

    // Verifica que se puede hacer login con password de longitud mínima
    public function test_it_logs_in_with_minimum_valid_password(): void
    {
        // Registrar usuario con password mínima
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Abc123!@',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Login
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Abc123!@'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $this->assertInstanceOf(User::class, $user);
    }

    // Verifica que se puede hacer login con password larga
    public function test_it_logs_in_with_long_password(): void
    {
        // Registrar usuario con password larga
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'MyVeryLongAndSecurePassword123!@#$%',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Login
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'MyVeryLongAndSecurePassword123!@#$%'
        ];

        $user = $this->loginUseCase->execute($loginData);

        $this->assertInstanceOf(User::class, $user);
    }

    // Verifica que no hace login con password que solo difiere en un carácter
    public function test_it_does_not_login_with_slightly_different_password(): void
    {
        // Registrar usuario
        $registerData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData);

        // Intentar login con password ligeramente diferente
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Test1234@' // Cambió ! por @
        ];

        $this->expectException(InvalidCredentialsException::class);

        $this->loginUseCase->execute($loginData);
    }

    // Verifica que se puede hacer login de diferentes usuarios
    public function test_it_logs_in_different_users_correctly(): void
    {
        // Registrar dos usuarios
        $registerData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData1);

        $registerData2 = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => 'Different1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData2);

        // Login del primer usuario
        $loginData1 = [
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];
        $user1 = $this->loginUseCase->execute($loginData1);

        // Login del segundo usuario
        $loginData2 = [
            'email' => 'maria@example.com',
            'password' => 'Different1234!'
        ];
        $user2 = $this->loginUseCase->execute($loginData2);

        $this->assertEquals('juan@example.com', $user1->email()->value());
        $this->assertEquals('maria@example.com', $user2->email()->value());
        $this->assertNotEquals($user1->id()->value(), $user2->id()->value());
    }

    // Verifica que no hace login de un usuario con password de otro
    public function test_it_does_not_login_user_with_another_users_password(): void
    {
        // Registrar dos usuarios
        $registerData1 = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData1);

        $registerData2 = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => 'Different1234!',
            'roles' => ['follower']
        ];
        $this->registerUseCase->execute($registerData2);

        // Intentar login de Juan con password de María
        $loginData = [
            'email' => 'juan@example.com',
            'password' => 'Different1234!'
        ];

        $this->expectException(InvalidCredentialsException::class);

        $this->loginUseCase->execute($loginData);
    }
}
