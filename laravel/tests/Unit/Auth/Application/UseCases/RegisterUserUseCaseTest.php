<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Application\UseCases;

use PHPUnit\Framework\TestCase;
use Src\Auth\Application\UseCases\RegisterUserUseCase;
use Src\Auth\Application\Ports\Out\UserRepository;
use Src\Auth\Domain\User\User;
use Src\Shared\Domain\Exceptions\MultipleDomainException;

final class RegisterUserUseCaseTest extends TestCase
{
    /**
     * @var UserRepository&\PHPUnit\Framework\MockObject\MockObject Mock del repositorio de usuarios
     */
    private UserRepository $userRepository;
    /**
     * @var RegisterUserUseCase Caso de uso que se va a probar
     */
    private RegisterUserUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->useCase = new RegisterUserUseCase($this->userRepository);
    }

    // Verifica que se puede registrar un usuario con datos válidos
    public function test_it_registers_user_with_valid_data(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $user = $this->useCase->execute($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Juan Pérez', $user->nameValue());
        $this->assertEquals('juan@example.com', $user->email()->value());
        $this->assertEquals('Test1234!', $user->password()->value());
    }

    // Verifica que lanza excepción cuando el email ya existe
    public function test_it_throws_exception_when_email_already_exists(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(MultipleDomainException::class);

        $this->useCase->execute($userData);
    }

    // Verifica que acumula errores cuando todos los campos están vacíos
    public function test_it_accumulates_errors_when_all_fields_are_empty(): void
    {
        $userData = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('exists');

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
            $this->assertCount(1, $errors['name']);
            $this->assertCount(1, $errors['email']);
            $this->assertCount(1, $errors['password']);
        }
    }

    // Verifica que acumula error cuando el nombre está vacío
    public function test_it_accumulates_error_when_name_is_empty(): void
    {
        $userData = [
            'name' => '',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayNotHasKey('email', $errors);
            $this->assertArrayNotHasKey('password', $errors);
        }
    }

    // Verifica que acumula error cuando el email está vacío
    public function test_it_accumulates_error_when_email_is_empty(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => '',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayNotHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayNotHasKey('password', $errors);
        }
    }

    // Verifica que acumula error cuando la contraseña está vacía
    public function test_it_accumulates_error_when_password_is_empty(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => ''
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayNotHasKey('name', $errors);
            $this->assertArrayNotHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que acumula error cuando el nombre es demasiado corto
    public function test_it_accumulates_error_when_name_is_too_short(): void
    {
        $userData = [
            'name' => 'AB',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('name', $errors);
        }
    }

    // Verifica que acumula error cuando el nombre es demasiado largo
    public function test_it_accumulates_error_when_name_is_too_long(): void
    {
        $userData = [
            'name' => str_repeat('a', 101),
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('name', $errors);
        }
    }

    // Verifica que acumula error cuando el email tiene formato inválido
    public function test_it_accumulates_error_when_email_has_invalid_format(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'invalid-email',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('email', $errors);
        }
    }

    // Verifica que acumula error cuando la contraseña es demasiado corta
    public function test_it_accumulates_error_when_password_is_too_short(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test12!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que acumula error cuando la contraseña no tiene mayúsculas
    public function test_it_accumulates_error_when_password_has_no_uppercase(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que acumula error cuando la contraseña no tiene minúsculas
    public function test_it_accumulates_error_when_password_has_no_lowercase(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'TEST1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que acumula error cuando la contraseña no tiene números
    public function test_it_accumulates_error_when_password_has_no_numbers(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'TestTest!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que acumula error cuando la contraseña no tiene caracteres especiales
    public function test_it_accumulates_error_when_password_has_no_special_characters(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que acumula múltiples errores cuando varios campos son inválidos
    public function test_it_accumulates_multiple_errors_when_multiple_fields_are_invalid(): void
    {
        $userData = [
            'name' => 'AB',
            'email' => 'invalid-email',
            'password' => 'short'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que acumula error de email duplicado junto con otros errores de validación
    public function test_it_accumulates_email_duplicate_error_with_other_validation_errors(): void
    {
        $userData = [
            'name' => 'AB',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertCount(1, $errors['email']); // Email duplicado + error de validación si hubiera
        }
    }

    // Verifica que no verifica unicidad del email si el email tiene errores de formato
    public function test_it_does_not_check_email_uniqueness_when_email_has_format_errors(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'invalid-email',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('exists');

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('email', $errors);
            $this->assertCount(1, $errors['email']); // Solo error de formato, no de duplicado
        }
    }

    // Verifica que no se guarda el usuario si hay errores acumulados
    public function test_it_does_not_save_user_when_errors_are_accumulated(): void
    {
        $userData = [
            'name' => '',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
        } catch (MultipleDomainException $e) {
            // Se espera la excepción
        }
    }

    // Verifica que maneja correctamente campos faltantes en el array de entrada
    public function test_it_handles_missing_fields_in_input_array(): void
    {
        $userData = []; // Sin campos

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        try {
            $this->useCase->execute($userData);
            $this->fail('Expected MultipleDomainException was not thrown');
        } catch (MultipleDomainException $e) {
            $errors = $e->errors();
            
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('password', $errors);
        }
    }

    // Verifica que registra correctamente con nombre de longitud mínima válida
    public function test_it_registers_user_with_minimum_valid_name_length(): void
    {
        $userData = [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $user = $this->useCase->execute($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Ana', $user->nameValue());
    }

    // Verifica que registra correctamente con nombre de longitud máxima válida
    public function test_it_registers_user_with_maximum_valid_name_length(): void
    {
        $longName = str_repeat('a', 100);
        $userData = [
            'name' => $longName,
            'email' => 'user@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $user = $this->useCase->execute($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($longName, $user->nameValue());
    }

    // Verifica que registra correctamente con contraseña de longitud mínima válida
    public function test_it_registers_user_with_minimum_valid_password_length(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Abc123!@'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $user = $this->useCase->execute($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Abc123!@', $user->password()->value());
    }

    // Verifica que el usuario registrado tiene un ID válido generado
    public function test_it_registered_user_has_valid_generated_id(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $user = $this->useCase->execute($userData);

        $this->assertNotEmpty($user->id()->value());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $user->id()->value()
        );
    }

    // Verifica que llama al método save del repositorio con el usuario correcto
    public function test_it_calls_repository_save_with_correct_user(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $savedUser = null;
        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function($user) use (&$savedUser) {
                $savedUser = $user;
            });

        $user = $this->useCase->execute($userData);

        $this->assertSame($user, $savedUser);
    }

    // Verifica que trimea correctamente los espacios del email antes de validar
    public function test_it_trims_email_before_validation(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => '  juan@example.com  ',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->with($this->callback(function($email) {
                return $email->value() === 'juan@example.com';
            }))
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $user = $this->useCase->execute($userData);

        $this->assertEquals('juan@example.com', $user->email()->value());
    }

    // Verifica que trimea correctamente los espacios del nombre antes de validar
    public function test_it_trims_name_before_validation(): void
    {
        $userData = [
            'name' => '  Juan Pérez  ',
            'email' => 'juan@example.com',
            'password' => 'Test1234!'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $user = $this->useCase->execute($userData);

        $this->assertEquals('Juan Pérez', $user->nameValue());
    }
}