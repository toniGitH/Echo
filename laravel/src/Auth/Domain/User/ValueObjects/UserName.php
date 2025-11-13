<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\ValueObjects;

use Src\Auth\Domain\User\Exceptions\EmptyUserNameException;
use Src\Auth\Domain\User\Exceptions\InvalidUserNameException;

final class UserName
{
    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidUserName($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);
        
        // Primero verificar si está vacío
        if (empty($value)) {
            throw new EmptyUserNameException();
        }
        
        return new self($value);
    }

    private function ensureIsValidUserName(string $value): void
    {
        $length = mb_strlen($value);

        // Solo validar longitud (ya verificamos vacío en fromString)
        if ($length < 3 || $length > 100) {
            throw new InvalidUserNameException();
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserName $other): bool
    {
        return $this->value === $other->value();
    }
}