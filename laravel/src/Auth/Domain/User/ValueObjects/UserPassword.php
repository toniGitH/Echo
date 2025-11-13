<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\ValueObjects;

use Src\Auth\Domain\User\Exceptions\EmptyPasswordException;
use Src\Auth\Domain\User\Exceptions\InvalidPasswordException;

final class UserPassword
{
    private const REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|;:\'",.<>\/?¿])/';

    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidPassword($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private function ensureIsValidPassword(string $password): void
    {
        // Primero verificar si está vacío
        if (trim($password) === '') {
            throw new EmptyPasswordException();
        }
        
        // Después, verificar longitud mínima, regex y no permitir espacios al inicio/final
        if (strlen($password) < 8 
            || !preg_match(self::REGEX, $password) 
            || $password !== trim($password)
        ) {
            throw new InvalidPasswordException();
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}