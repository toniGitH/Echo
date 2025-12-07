<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Domain\User\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Auth\Domain\User\ValueObjects\UserRole;
use Src\Auth\Domain\User\Exceptions\EmptyRoleException;
use Src\Auth\Domain\User\Exceptions\InvalidRoleException;

final class UserRoleTest extends TestCase
{
    // Verifica que se puede crear un rol de admin válido
    public function test_it_creates_valid_admin_role(): void
    {
        $role = UserRole::admin();
        
        $this->assertInstanceOf(UserRole::class, $role);
        $this->assertEquals('admin', $role->value());
    }

    // Verifica que se puede crear un rol de client válido
    public function test_it_creates_valid_client_role(): void
    {
        $role = UserRole::client();
        
        $this->assertInstanceOf(UserRole::class, $role);
        $this->assertEquals('client', $role->value());
    }

    // Verifica que se puede crear un rol de follower válido
    public function test_it_creates_valid_follower_role(): void
    {
        $role = UserRole::follower();
        
        $this->assertInstanceOf(UserRole::class, $role);
        $this->assertEquals('follower', $role->value());
    }

    // Verifica que se puede crear un rol válido desde string
    public function test_it_creates_valid_role_from_string(): void
    {
        $role = UserRole::fromString('admin');
        
        $this->assertInstanceOf(UserRole::class, $role);
        $this->assertEquals('admin', $role->value());
    }

    // Verifica que se puede crear un rol válido desde string con espacios
    public function test_it_creates_valid_role_from_string_with_spaces(): void
    {
        $role = UserRole::fromString(' admin ');
        
        $this->assertInstanceOf(UserRole::class, $role);
        $this->assertEquals('admin', $role->value());
    }

    // Verifica que lanza excepción cuando el rol está vacío
    public function test_it_throws_exception_when_role_is_empty(): void
    {
        $this->expectException(EmptyRoleException::class);
        $this->expectExceptionMessage('messages.user.EMPTY_ROLE');
        
        UserRole::fromString('');
    }

    // Verifica que lanza excepción cuando el rol solo tiene espacios
    public function test_it_throws_exception_when_role_is_only_whitespace(): void
    {
        $this->expectException(EmptyRoleException::class);
        $this->expectExceptionMessage('messages.user.EMPTY_ROLE');
        
        UserRole::fromString('   ');
    }

    // Verifica que lanza excepción cuando el rol es inválido
    public function test_it_throws_exception_when_role_is_invalid(): void
    {
        $this->expectException(InvalidRoleException::class);
        $this->expectExceptionMessage('messages.user.INVALID_ROLE');
        
        UserRole::fromString('invalid-role');
    }

    // Verifica que dos roles con el mismo valor son iguales
    public function test_it_equals_when_same_value(): void
    {
        $role1 = UserRole::admin();
        $role2 = UserRole::admin();
        
        $this->assertTrue($role1->equals($role2));
    }

    // Verifica que dos roles con diferente valor no son iguales
    public function test_it_not_equals_when_different_value(): void
    {
        $role1 = UserRole::admin();
        $role2 = UserRole::client();
        
        $this->assertFalse($role1->equals($role2));
    }
}
