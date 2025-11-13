<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Domain\User\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Auth\Domain\User\ValueObjects\UserPassword;
use Src\Auth\Domain\User\Exceptions\EmptyPasswordException;
use Src\Auth\Domain\User\Exceptions\InvalidPasswordException;

final class UserPasswordTest extends TestCase
{
    // Verifica que se puede crear una UserPassword válida con todos los requisitos
    public function test_it_creates_valid_password_with_all_requirements(): void
    {
        $password = UserPassword::fromString('Test1234!');
        
        $this->assertInstanceOf(UserPassword::class, $password);
        $this->assertEquals('Test1234!', $password->value());
    }

    // Verifica que se puede crear una UserPassword válida con longitud mínima
    public function test_it_creates_valid_password_with_minimum_length(): void
    {
        $password = UserPassword::fromString('Abc123!@');
        
        $this->assertInstanceOf(UserPassword::class, $password);
        $this->assertEquals('Abc123!@', $password->value());
    }

    // Verifica que se puede crear una UserPassword válida con longitud extensa
    public function test_it_creates_valid_password_with_long_length(): void
    {
        $password = UserPassword::fromString('MyVeryLongAndSecurePassword123!@#$%');
        
        $this->assertInstanceOf(UserPassword::class, $password);
        $this->assertEquals('MyVeryLongAndSecurePassword123!@#$%', $password->value());
    }

    // Verifica que se puede crear una UserPassword con diversos caracteres especiales
    public function test_it_creates_valid_password_with_various_special_characters(): void
    {
        $specialChars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '-', '+', '=', '[', ']', '{', '}', '|', ';', ':', "'", '"', ',', '.', '<', '>', '/', '?', '¿'];
        
        foreach ($specialChars as $char) {
            $password = UserPassword::fromString("Test123{$char}");
            $this->assertInstanceOf(UserPassword::class, $password);
        }
    }

    // Verifica que lanza excepción cuando la contraseña está vacía
    public function test_it_throws_exception_when_password_is_empty(): void
    {
        $this->expectException(EmptyPasswordException::class);
        
        UserPassword::fromString('');
    }

    // Verifica que lanza excepción cuando la contraseña es muy corta (7 caracteres)
    public function test_it_throws_exception_when_password_is_too_short(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('Test12!');
    }

    // Verifica que lanza excepción cuando la contraseña no tiene mayúsculas
    public function test_it_throws_exception_when_password_has_no_uppercase(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('test1234!');
    }

    // Verifica que lanza excepción cuando la contraseña no tiene minúsculas
    public function test_it_throws_exception_when_password_has_no_lowercase(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('TEST1234!');
    }

    // Verifica que lanza excepción cuando la contraseña no tiene números
    public function test_it_throws_exception_when_password_has_no_numbers(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('TestTest!');
    }

    // Verifica que lanza excepción cuando la contraseña no tiene caracteres especiales
    public function test_it_throws_exception_when_password_has_no_special_characters(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('Test1234');
    }

    // Verifica que lanza excepción cuando la contraseña solo tiene letras
    public function test_it_throws_exception_when_password_has_only_letters(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('TestPassword');
    }

    // Verifica que lanza excepción cuando la contraseña solo tiene números
    public function test_it_throws_exception_when_password_has_only_numbers(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('12345678');
    }

    // Verifica que lanza excepción cuando la contraseña solo tiene caracteres especiales
    public function test_it_throws_exception_when_password_has_only_special_characters(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('!@#$%^&*');
    }

    // Verifica que lanza excepción cuando la contraseña tiene todas mayúsculas y números pero sin especiales
    public function test_it_throws_exception_when_password_has_uppercase_numbers_but_no_special(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('TEST1234');
    }

    // Verifica que lanza excepción cuando la contraseña tiene todas minúsculas y números pero sin especiales
    public function test_it_throws_exception_when_password_has_lowercase_numbers_but_no_special(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('test1234');
    }

    // Verifica que lanza excepción cuando la contraseña tiene letras y especiales pero sin números
    public function test_it_throws_exception_when_password_has_letters_special_but_no_numbers(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('TestTest!');
    }

    // Verifica que lanza excepción cuando la contraseña tiene solo un carácter
    public function test_it_throws_exception_when_password_is_one_character(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('A');
    }

    // Verifica que lanza excepción cuando la contraseña tiene 8 caracteres pero no cumple requisitos
    public function test_it_throws_exception_when_password_is_8_chars_but_invalid(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('abcdefgh');
    }

    // Verifica que no elimina espacios de la contraseña (los espacios no son caracteres especiales válidos)
    public function test_it_throws_exception_when_password_has_spaces_instead_of_special_chars(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('Test 1234');
    }

    // Verifica que acepta contraseña con múltiples mayúsculas
    public function test_it_accepts_password_with_multiple_uppercase(): void
    {
        $password = UserPassword::fromString('TESTtest123!');
        
        $this->assertInstanceOf(UserPassword::class, $password);
    }

    // Verifica que acepta contraseña con múltiples números
    public function test_it_accepts_password_with_multiple_numbers(): void
    {
        $password = UserPassword::fromString('Test123456!');
        
        $this->assertInstanceOf(UserPassword::class, $password);
    }

    // Verifica que acepta contraseña con múltiples caracteres especiales
    public function test_it_accepts_password_with_multiple_special_characters(): void
    {
        $password = UserPassword::fromString('Test123!@#');
        
        $this->assertInstanceOf(UserPassword::class, $password);
    }

    // Verifica que lanza excepción cuando solo contiene espacios
    public function test_it_throws_exception_when_password_is_only_whitespace(): void
    {
        $this->expectException(EmptyPasswordException::class);
        
        UserPassword::fromString('        ');
    }

    // Verifica que la contraseña no se recorta (trim) ya que los espacios podrían ser parte de ella
    public function test_it_does_not_trim_valid_password_with_spaces(): void
    {
        // Aunque tiene espacios al principio y final, si cumple requisitos debería ser válida
        // Sin embargo, los espacios NO cuentan como caracteres especiales según la regex
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('  Test123!  ');
    }

    // Verifica que acepta contraseña exactamente con 8 caracteres válidos
    public function test_it_accepts_password_with_exactly_8_valid_characters(): void
    {
        $password = UserPassword::fromString('Abc12!@#');
        
        $this->assertInstanceOf(UserPassword::class, $password);
    }

    // Verifica que lanza excepción con 7 caracteres aunque cumpla otros requisitos
    public function test_it_throws_exception_with_7_chars_meeting_other_requirements(): void
    {
        $this->expectException(InvalidPasswordException::class);
        
        UserPassword::fromString('Test12!');
    }

    // Verifica que acepta contraseña con caracteres unicode que cumplan requisitos
    public function test_it_handles_unicode_characters_correctly(): void
    {
        // La contraseña tiene caracteres válidos (mayúscula, minúscula, número, especial)
        $password = UserPassword::fromString('TéstÑ123!');
        
        $this->assertInstanceOf(UserPassword::class, $password);
    }
}