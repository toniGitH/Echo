<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Domain\User\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Auth\Domain\User\ValueObjects\UserId;
use Src\Auth\Domain\User\Exceptions\InvalidUserIdException;

final class UserIdTest extends TestCase
{
    // Verifica que se puede generar un nuevo UserId válido
    public function test_it_generates_valid_user_id(): void
    {
        $userId = UserId::generate();
        
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertNotEmpty($userId->value());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $userId->value()
        );
    }

    // Verifica que se puede crear un UserId desde un UUID v4 válido
    public function test_it_creates_user_id_from_valid_uuid_v4(): void
    {
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';
        
        $userId = UserId::fromString($validUuid);
        
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertEquals($validUuid, $userId->value());
    }

    // Verifica que lanza excepción cuando el UUID está vacío
    public function test_it_throws_exception_when_uuid_is_empty(): void
    {
        $this->expectException(InvalidUserIdException::class);
        
        UserId::fromString('');
    }

    // Verifica que lanza excepción cuando el UUID solo contiene espacios
    public function test_it_throws_exception_when_uuid_is_only_whitespace(): void
    {
        $this->expectException(InvalidUserIdException::class);
        
        UserId::fromString('   ');
    }

    // Verifica que lanza excepción cuando el UUID tiene formato inválido
    public function test_it_throws_exception_when_uuid_has_invalid_format(): void
    {
        $this->expectException(InvalidUserIdException::class);
        
        UserId::fromString('not-a-valid-uuid');
    }

    // Verifica que lanza excepción cuando el UUID no es versión 4
    public function test_it_throws_exception_when_uuid_is_not_version_4(): void
    {
        $this->expectException(InvalidUserIdException::class);
        
        // UUID v1
        UserId::fromString('550e8400-e29b-11d4-a716-446655440000');
    }

    // Verifica que lanza excepción cuando el UUID tiene caracteres inválidos
    public function test_it_throws_exception_when_uuid_has_invalid_characters(): void
    {
        $this->expectException(InvalidUserIdException::class);
        
        UserId::fromString('550e8400-e29b-41d4-a716-44665544000g');
    }

    // Verifica que lanza excepción cuando el UUID es muy corto
    public function test_it_throws_exception_when_uuid_is_too_short(): void
    {
        $this->expectException(InvalidUserIdException::class);
        
        UserId::fromString('550e8400-e29b-41d4');
    }

    // Verifica que lanza excepción cuando el UUID es muy largo
    public function test_it_throws_exception_when_uuid_is_too_long(): void
    {
        $this->expectException(InvalidUserIdException::class);
        
        UserId::fromString('550e8400-e29b-41d4-a716-446655440000-extra');
    }

    // Verifica que dos UserIds con el mismo valor son iguales
    public function test_it_equals_when_same_value(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId1 = UserId::fromString($uuid);
        $userId2 = UserId::fromString($uuid);
        
        $this->assertTrue($userId1->equals($userId2));
    }

    // Verifica que dos UserIds con diferente valor no son iguales
    public function test_it_not_equals_when_different_value(): void
    {
        $userId1 = UserId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $userId2 = UserId::fromString('660e8400-e29b-41d4-a716-446655440000');
        
        $this->assertFalse($userId1->equals($userId2));
    }

    // Verifica que cada generación de UserId crea un valor único
    public function test_it_generates_unique_ids(): void
    {
        $userId1 = UserId::generate();
        $userId2 = UserId::generate();
        
        $this->assertNotEquals($userId1->value(), $userId2->value());
    }
}