<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\ValueObjects;

use Src\Auth\Domain\User\Exceptions\InvalidUserIdException;
use Ramsey\Uuid\Uuid;

/**
 * Value Object para el ID del usuario.
 * Representa un identificador único UUID v4.
 */
final class UserId
{
    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        $this->value = $value;
    }

    public static function generate(): self // Crear un ID nuevo (registro)
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self // Reconstruye un ID desde BD
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    private function ensureIsValidUuid(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidUserIdException('messages.user.EMPTY_USER_ID');
        }

        if (!Uuid::isValid($value)) {
            throw new InvalidUserIdException('messages.user.INVALID_USER_ID_FORMAT');
        }

        // Validar versión 4 explícitamente (tercer bloque empieza por '4')
        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $value)) {
            throw new InvalidUserIdException('messages.user.INVALID_USER_ID_FORMAT');
        }
    }

    /**
     * Compara este ID con otro por su valor.
     * Aunque no se use en producción, se incluye por consistencia con principios de DDD.
     * 
     * @param UserId $other
     * @return bool
     */
    public function equals(UserId $other): bool
    {
        return $this->value === $other->value;
    }
}