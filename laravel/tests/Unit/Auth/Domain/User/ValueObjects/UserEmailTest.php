<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Domain\User\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Auth\Domain\User\ValueObjects\UserEmail;
use Src\Auth\Domain\User\Exceptions\EmptyEmailException;
use Src\Auth\Domain\User\Exceptions\InvalidEmailException;

final class UserEmailTest extends TestCase
{
    // Verifica que se puede crear un UserEmail válido con formato estándar
    public function test_it_creates_valid_email_with_standard_format(): void
    {
        $email = UserEmail::fromString('user@example.com');
        
        $this->assertInstanceOf(UserEmail::class, $email);
        $this->assertEquals('user@example.com', $email->value());
    }

    // Verifica que se puede crear un UserEmail con subdominios
    public function test_it_creates_valid_email_with_subdomain(): void
    {
        $email = UserEmail::fromString('user@mail.example.com');
        
        $this->assertInstanceOf(UserEmail::class, $email);
        $this->assertEquals('user@mail.example.com', $email->value());
    }

    // Verifica que se puede crear un UserEmail con números
    public function test_it_creates_valid_email_with_numbers(): void
    {
        $email = UserEmail::fromString('user123@example456.com');
        
        $this->assertInstanceOf(UserEmail::class, $email);
        $this->assertEquals('user123@example456.com', $email->value());
    }

    // Verifica que se puede crear un UserEmail con guiones y puntos
    public function test_it_creates_valid_email_with_hyphens_and_dots(): void
    {
        $email = UserEmail::fromString('user.name-test@example-domain.com');
        
        $this->assertInstanceOf(UserEmail::class, $email);
        $this->assertEquals('user.name-test@example-domain.com', $email->value());
    }

    // Verifica que se puede crear un UserEmail con caracteres especiales permitidos
    public function test_it_creates_valid_email_with_special_characters(): void
    {
        $email = UserEmail::fromString('user+tag@example.com');
        
        $this->assertInstanceOf(UserEmail::class, $email);
        $this->assertEquals('user+tag@example.com', $email->value());
    }

    // Verifica que elimina espacios en blanco al inicio y final
    public function test_it_trims_whitespace_from_email(): void
    {
        $email = UserEmail::fromString('  user@example.com  ');
        
        $this->assertEquals('user@example.com', $email->value());
    }

    // Verifica que lanza excepción cuando el email está vacío
    public function test_it_throws_exception_when_email_is_empty(): void
    {
        $this->expectException(EmptyEmailException::class);
        
        UserEmail::fromString('');
    }

    // Verifica que lanza excepción cuando el email solo contiene espacios
    public function test_it_throws_exception_when_email_is_only_whitespace(): void
    {
        $this->expectException(EmptyEmailException::class);
        
        UserEmail::fromString('   ');
    }

    // Verifica que lanza excepción cuando el email no tiene arroba
    public function test_it_throws_exception_when_email_has_no_at_symbol(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('userexample.com');
    }

    // Verifica que lanza excepción cuando el email no tiene dominio
    public function test_it_throws_exception_when_email_has_no_domain(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user@');
    }

    // Verifica que lanza excepción cuando el email no tiene usuario
    public function test_it_throws_exception_when_email_has_no_user(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('@example.com');
    }

    // Verifica que lanza excepción cuando el email tiene múltiples arrobas
    public function test_it_throws_exception_when_email_has_multiple_at_symbols(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user@@example.com');
    }

    // Verifica que lanza excepción cuando el email no tiene extensión de dominio
    public function test_it_throws_exception_when_email_has_no_domain_extension(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user@example');
    }

    // Verifica que lanza excepción cuando el email tiene espacios en medio
    public function test_it_throws_exception_when_email_has_spaces_in_middle(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user name@example.com');
    }

    // Verifica que lanza excepción cuando el email tiene caracteres inválidos después de la @
    public function test_it_throws_exception_when_email_has_invalid_characters(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('username@example#1.com');
    }

    // Verifica que lanza excepción cuando el email comienza con punto
    public function test_it_throws_exception_when_email_starts_with_dot(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('.user@example.com');
    }

    // Verifica que lanza excepción cuando el email termina con punto antes de arroba
    public function test_it_throws_exception_when_email_ends_with_dot_before_at(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user.@example.com');
    }

    // Verifica que lanza excepción cuando el email tiene puntos consecutivos
    public function test_it_throws_exception_when_email_has_consecutive_dots(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user..name@example.com');
    }

    // Verifica que lanza excepción cuando el dominio comienza con guion
    public function test_it_throws_exception_when_domain_starts_with_hyphen(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user@-example.com');
    }

    // Verifica que lanza excepción cuando el dominio termina con guion
    public function test_it_throws_exception_when_domain_ends_with_hyphen(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('user@example-.com');
    }

    // Verifica que lanza excepción con formato completamente inválido
    public function test_it_throws_exception_with_completely_invalid_format(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('not-an-email');
    }

    // Verifica que lanza excepción cuando solo hay un carácter
    public function test_it_throws_exception_when_only_one_character(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        UserEmail::fromString('a');
    }

    // Verifica que acepta emails con dominios largos
    public function test_it_accepts_email_with_long_domain(): void
    {
        $email = UserEmail::fromString('user@subdomain.example.co.uk');
        
        $this->assertInstanceOf(UserEmail::class, $email);
        $this->assertEquals('user@subdomain.example.co.uk', $email->value());
    }

    // Verifica que acepta emails con guión bajo en usuario
    public function test_it_accepts_email_with_underscore_in_user(): void
    {
        $email = UserEmail::fromString('user_name@example.com');
        
        $this->assertInstanceOf(UserEmail::class, $email);
        $this->assertEquals('user_name@example.com', $email->value());
    }

    // Verifica que dos UserEmails con el mismo valor son iguales
    public function test_it_equals_when_same_value(): void
    {
        $email1 = UserEmail::fromString('user@example.com');
        $email2 = UserEmail::fromString('user@example.com');
        
        $this->assertTrue($email1->equals($email2));
    }

    // Verifica que dos UserEmails con diferente valor no son iguales
    public function test_it_not_equals_when_different_value(): void
    {
        $email1 = UserEmail::fromString('user1@example.com');
        $email2 = UserEmail::fromString('user2@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }

    // Verifica que la comparación de igualdad es case-sensitive
    public function test_it_equals_is_case_sensitive(): void
    {
        $email1 = UserEmail::fromString('User@Example.COM');
        $email2 = UserEmail::fromString('user@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }
}