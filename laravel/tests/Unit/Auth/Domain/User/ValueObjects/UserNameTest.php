<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Domain\User\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Auth\Domain\User\ValueObjects\UserName;
use Src\Auth\Domain\User\Exceptions\EmptyUserNameException;
use Src\Auth\Domain\User\Exceptions\InvalidUserNameException;

final class UserNameTest extends TestCase
{
    // Verifica que se puede crear un UserName válido con longitud mínima
    public function test_it_creates_valid_username_with_minimum_length(): void
    {
        $userName = UserName::fromString('Ana');
        
        $this->assertInstanceOf(UserName::class, $userName);
        $this->assertEquals('Ana', $userName->value());
    }

    // Verifica que se puede crear un UserName válido con longitud media
    public function test_it_creates_valid_username_with_medium_length(): void
    {
        $userName = UserName::fromString('Juan Pérez García');
        
        $this->assertInstanceOf(UserName::class, $userName);
        $this->assertEquals('Juan Pérez García', $userName->value());
    }

    // Verifica que se puede crear un UserName válido con longitud máxima
    public function test_it_creates_valid_username_with_maximum_length(): void
    {
        $longName = str_repeat('a', 100);
        $userName = UserName::fromString($longName);
        
        $this->assertInstanceOf(UserName::class, $userName);
        $this->assertEquals($longName, $userName->value());
    }

    // Verifica que elimina espacios en blanco al inicio y final
    public function test_it_trims_whitespace_from_username(): void
    {
        $userName = UserName::fromString('  Juan  ');
        
        $this->assertEquals('Juan', $userName->value());
    }

    // Verifica que lanza excepción cuando el nombre está vacío
    public function test_it_throws_exception_when_username_is_empty(): void
    {
        $this->expectException(EmptyUserNameException::class);
        
        UserName::fromString('');
    }

    // Verifica que lanza excepción cuando el nombre solo contiene espacios
    public function test_it_throws_exception_when_username_is_only_whitespace(): void
    {
        $this->expectException(EmptyUserNameException::class);
        
        UserName::fromString('   ');
    }

    // Verifica que lanza excepción cuando el nombre es muy corto (2 caracteres)
    public function test_it_throws_exception_when_username_is_too_short(): void
    {
        $this->expectException(InvalidUserNameException::class);
        
        UserName::fromString('AB');
    }

    // Verifica que lanza excepción cuando el nombre es muy corto (1 carácter)
    public function test_it_throws_exception_when_username_is_one_character(): void
    {
        $this->expectException(InvalidUserNameException::class);
        
        UserName::fromString('A');
    }

    // Verifica que lanza excepción cuando el nombre es muy largo (101 caracteres)
    public function test_it_throws_exception_when_username_is_too_long(): void
    {
        $this->expectException(InvalidUserNameException::class);
        
        $tooLongName = str_repeat('a', 101);
        UserName::fromString($tooLongName);
    }

    // Verifica que lanza excepción cuando el nombre es extremadamente largo
    public function test_it_throws_exception_when_username_is_extremely_long(): void
    {
        $this->expectException(InvalidUserNameException::class);
        
        $tooLongName = str_repeat('a', 500);
        UserName::fromString($tooLongName);
    }

    // Verifica que acepta nombres con caracteres especiales y acentos
    public function test_it_accepts_username_with_special_characters(): void
    {
        $userName = UserName::fromString('María José O\'Connor-Smith');
        
        $this->assertInstanceOf(UserName::class, $userName);
        $this->assertEquals('María José O\'Connor-Smith', $userName->value());
    }

    // Verifica que acepta nombres con números
    public function test_it_accepts_username_with_numbers(): void
    {
        $userName = UserName::fromString('Juan 123');
        
        $this->assertInstanceOf(UserName::class, $userName);
        $this->assertEquals('Juan 123', $userName->value());
    }

    // Verifica que dos UserNames con el mismo valor son iguales
    public function test_it_equals_when_same_value(): void
    {
        $userName1 = UserName::fromString('Juan Pérez');
        $userName2 = UserName::fromString('Juan Pérez');
        
        $this->assertTrue($userName1->equals($userName2));
    }

    // Verifica que dos UserNames con diferente valor no son iguales
    public function test_it_not_equals_when_different_value(): void
    {
        $userName1 = UserName::fromString('Juan Pérez');
        $userName2 = UserName::fromString('María García');
        
        $this->assertFalse($userName1->equals($userName2));
    }

    // Verifica que la comparación de igualdad es case-sensitive
    public function test_it_equals_is_case_sensitive(): void
    {
        $userName1 = UserName::fromString('Juan Pérez');
        $userName2 = UserName::fromString('juan pérez');
        
        $this->assertFalse($userName1->equals($userName2));
    }

    // Verifica que maneja correctamente caracteres UTF-8 multibyte
    public function test_it_handles_multibyte_characters_correctly(): void
    {
        $userName = UserName::fromString('日本語名前');
        
        $this->assertInstanceOf(UserName::class, $userName);
        $this->assertEquals('日本語名前', $userName->value());
    }

    // Verifica que cuenta correctamente la longitud con caracteres multibyte
    public function test_it_counts_multibyte_characters_correctly(): void
    {
        // Cadena de 99 caracteres japoneses (válida)
        $validName = str_repeat('あ', 99);
        $userName = UserName::fromString($validName);
        
        $this->assertInstanceOf(UserName::class, $userName);
    }

    // Verifica que lanza excepción con caracteres multibyte cuando excede el límite
    public function test_it_throws_exception_with_multibyte_characters_when_too_long(): void
    {
        $this->expectException(InvalidUserNameException::class);
        
        // Cadena de 101 caracteres japoneses (inválida)
        $tooLongName = str_repeat('あ', 101);
        UserName::fromString($tooLongName);
    }
}