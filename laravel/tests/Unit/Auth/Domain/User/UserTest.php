<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Domain\User;

use PHPUnit\Framework\TestCase;
use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\ValueObjects\UserId;
use Src\Auth\Domain\User\ValueObjects\UserName;
use Src\Auth\Domain\User\ValueObjects\UserEmail;
use Src\Auth\Domain\User\ValueObjects\UserPassword;
use Src\Auth\Domain\User\ValueObjects\UserRole;

final class UserTest extends TestCase
{
    // Verifica que se puede crear un usuario con Value Objects válidos
    public function test_it_creates_user_with_valid_value_objects(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(UserId::class, $user->id());
        $this->assertSame($name, $user->name());
        $this->assertSame($email, $user->email());
        $this->assertSame($password, $user->password());
    }

    // Verifica que el ID se genera automáticamente al crear un usuario
    public function test_it_generates_id_automatically_when_creating_user(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertNotEmpty($user->id()->value());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $user->id()->value()
        );
    }

    // Verifica que cada usuario creado tiene un ID único
    public function test_it_generates_unique_id_for_each_user(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user1 = User::create($name, $email, $password, [UserRole::follower()]);
        $user2 = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertNotEquals($user1->id()->value(), $user2->id()->value());
    }

    // Verifica que el método name() devuelve el Value Object UserName
    public function test_it_returns_username_value_object(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(UserName::class, $user->name());
        $this->assertEquals('Juan Pérez', $user->name()->value());
    }

    // Verifica que el método nameValue() devuelve el valor string del nombre
    public function test_it_returns_name_string_value(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertIsString($user->nameValue());
        $this->assertEquals('Juan Pérez', $user->nameValue());
    }

    // Verifica que el método email() devuelve el Value Object UserEmail
    public function test_it_returns_email_value_object(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(UserEmail::class, $user->email());
        $this->assertEquals('juan@example.com', $user->email()->value());
    }

    // Verifica que el método password() devuelve el Value Object UserPassword
    public function test_it_returns_password_value_object(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(UserPassword::class, $user->password());
        $this->assertEquals('Test1234!', $user->password()->value());
    }

    // Verifica que toArray() devuelve un array con todos los datos del usuario
    public function test_it_converts_user_to_array(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        $array = $user->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('password', $array);
    }

    // Verifica que toArray() contiene los valores correctos de los Value Objects
    public function test_it_array_contains_correct_values(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        $array = $user->toArray();
        
        $this->assertEquals($user->id()->value(), $array['id']);
        $this->assertEquals('Juan Pérez', $array['name']);
        $this->assertEquals('juan@example.com', $array['email']);
        $this->assertEquals('Test1234!', $array['password']);
    }

    // Verifica que toArray() devuelve todos los valores como strings
    public function test_it_array_contains_only_string_values(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        $array = $user->toArray();
        
        $this->assertIsString($array['id']);
        $this->assertIsString($array['name']);
        $this->assertIsString($array['email']);
        $this->assertIsString($array['password']);
    }

    // Verifica que se puede crear un usuario con nombre mínimo válido
    public function test_it_creates_user_with_minimum_valid_name(): void
    {
        $name = UserName::fromString('Ana');
        $email = UserEmail::fromString('ana@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Ana', $user->nameValue());
    }

    // Verifica que se puede crear un usuario con nombre máximo válido
    public function test_it_creates_user_with_maximum_valid_name(): void
    {
        $longName = str_repeat('a', 100);
        $name = UserName::fromString($longName);
        $email = UserEmail::fromString('user@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($longName, $user->nameValue());
    }

    // Verifica que se puede crear un usuario con contraseña mínima válida
    public function test_it_creates_user_with_minimum_valid_password(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Abc123!@');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Abc123!@', $user->password()->value());
    }

    // Verifica que se puede crear un usuario con email complejo pero válido
    public function test_it_creates_user_with_complex_valid_email(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('user.name+tag@sub.example.co.uk');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('user.name+tag@sub.example.co.uk', $user->email()->value());
    }

    // Verifica que se puede crear un usuario con caracteres especiales en el nombre
    public function test_it_creates_user_with_special_characters_in_name(): void
    {
        $name = UserName::fromString('María José O\'Connor-Smith');
        $email = UserEmail::fromString('maria@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('María José O\'Connor-Smith', $user->nameValue());
    }

    // Verifica que los Value Objects dentro del usuario mantienen su inmutabilidad
    public function test_it_maintains_value_objects_immutability(): void
    {
        $name = UserName::fromString('Juan Pérez');
        $email = UserEmail::fromString('juan@example.com');
        $password = UserPassword::fromString('Test1234!');
        
        $user = User::create($name, $email, $password, [UserRole::follower()]);
        
        // Verificar que obtenemos la misma referencia del objeto
        $this->assertSame($name, $user->name());
        $this->assertSame($email, $user->email());
        $this->assertSame($password, $user->password());
    }

    // Verifica que dos usuarios con los mismos datos tienen IDs diferentes
    public function test_it_different_users_have_different_ids_even_with_same_data(): void
    {
        $name1 = UserName::fromString('Juan Pérez');
        $email1 = UserEmail::fromString('juan@example.com');
        $password1 = UserPassword::fromString('Test1234!');
        
        $name2 = UserName::fromString('Juan Pérez');
        $email2 = UserEmail::fromString('juan@example.com');
        $password2 = UserPassword::fromString('Test1234!');
        
        $user1 = User::create($name1, $email1, $password1, [UserRole::follower()]);
        $user2 = User::create($name2, $email2, $password2, [UserRole::follower()]);
        
        $this->assertNotEquals($user1->id()->value(), $user2->id()->value());
    }

    // Verifica que fromPrimitives reconstruye un usuario correctamente desde datos primitivos
    public function test_it_reconstructs_user_from_primitives(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $name = 'Juan Pérez';
        $email = 'juan@example.com';
        
        $user = User::fromPrimitives($id, $name, $email, ['follower']);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($id, $user->id()->value());
        $this->assertEquals($name, $user->name()->value());
        $this->assertEquals($email, $user->email()->value());
    }

    // Verifica que fromPrimitives crea un usuario sin contraseña (password = null)
    public function test_it_reconstructs_user_from_primitives_without_password(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $name = 'Juan Pérez';
        $email = 'juan@example.com';
        
        $user = User::fromPrimitives($id, $name, $email, ['follower']);
        
        $this->assertNull($user->password());
    }

    // Verifica que fromPrimitives lanza excepción cuando el ID es inválido
    public function test_it_throws_exception_when_from_primitives_receives_invalid_id(): void
    {
        $this->expectException(\Src\Auth\Domain\User\Exceptions\InvalidUserIdException::class);
        $this->expectExceptionMessage('messages.user.INVALID_USER_ID_FORMAT');
        
        User::fromPrimitives('invalid-uuid', 'Juan Pérez', 'juan@example.com', ['follower']);
    }

    // Verifica que fromPrimitives lanza excepción cuando el nombre es inválido
    public function test_it_throws_exception_when_from_primitives_receives_invalid_name(): void
    {
        $this->expectException(\Src\Auth\Domain\User\Exceptions\InvalidUserNameException::class);
        $this->expectExceptionMessage('messages.user.INVALID_USER_NAME');
        
        $id = '550e8400-e29b-41d4-a716-446655440000';
        User::fromPrimitives($id, 'AB', 'juan@example.com', ['follower']); // Nombre muy corto (mínimo 3 caracteres)
    }

    // Verifica que fromPrimitives lanza excepción cuando el email es inválido
    public function test_it_throws_exception_when_from_primitives_receives_invalid_email(): void
    {
        $this->expectException(\Src\Auth\Domain\User\Exceptions\InvalidEmailException::class);
        $this->expectExceptionMessage('messages.user.INVALID_EMAIL_FORMAT');
        
        $id = '550e8400-e29b-41d4-a716-446655440000';
        User::fromPrimitives($id, 'Juan Pérez', 'invalid-email', ['follower']);
    }

    // Verifica que toArray no incluye la clave 'password' cuando el usuario no tiene contraseña
    public function test_it_array_does_not_include_password_when_null(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $name = 'Juan Pérez';
        $email = 'juan@example.com';
        
        $user = User::fromPrimitives($id, $name, $email, ['follower']);
        $array = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('email', $array);
    }

    // Verifica que toArray contiene los valores correctos cuando no hay contraseña
    public function test_it_array_contains_correct_values_without_password(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $name = 'Juan Pérez';
        $email = 'juan@example.com';
        
        $user = User::fromPrimitives($id, $name, $email, ['follower']);
        $array = $user->toArray();
        
        $this->assertEquals($id, $array['id']);
        $this->assertEquals($name, $array['name']);
        $this->assertEquals($email, $array['email']);
        $this->assertCount(4, $array); // 4 elementos (id, name, email, roles)
    }
}