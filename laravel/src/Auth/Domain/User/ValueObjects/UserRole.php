<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\ValueObjects;

use Src\Auth\Domain\User\Exceptions\EmptyRoleException;
use Src\Auth\Domain\User\Exceptions\InvalidRoleException;

final class UserRole
{
    private const ROLE_CLIENT = 'client';
    private const ROLE_FOLLOWER = 'follower';
    private const ROLE_ADMIN = 'admin';

    private string $value;

    private function __construct(string $value)
    {
        // Normalización universal
        $value = trim($value);
        
        // Validación
        $this->validate($value);
        
        // Asignación
        $this->value = $value;
    }

    public static function fromString(string $role): self
    {
        // Solo delega al constructor
        return new self($role);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserRole $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(string $role): void
    {
        // Validación 1: No puede estar vacío (detecta: "", "0" y null)
        if (empty($role)) {
            throw new EmptyRoleException();
        }
        // Validación 2: Debe ser un rol válido (sólo admite client, follower y admin)
        // Compara el valor del rol con los roles disponibles de manera estricta (true -> ===)
        if (!in_array($role, self::availableRoles(), true)) {
            throw new InvalidRoleException();
        }
    }

    public static function availableRoles(): array
    {
        return [
            self::ROLE_CLIENT,
            self::ROLE_FOLLOWER,
            self::ROLE_ADMIN,
        ];
    }

    public static function client(): self
    {
        return new self(self::ROLE_CLIENT);
    }

    public static function follower(): self
    {
        return new self(self::ROLE_FOLLOWER);
    }

    public static function admin(): self
    {
        return new self(self::ROLE_ADMIN);
    }
}
