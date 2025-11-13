<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\ValueObjects;

use Src\Auth\Domain\User\Exceptions\EmptyEmailException;
use Src\Auth\Domain\User\Exceptions\InvalidEmailException;

final class UserEmail
{
    private string $value;

    private function __construct(string $value)
    {
        $value = trim($value);
        $this->ensureIsValidEmail($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private function ensureIsValidEmail(string $email): void
    {
        // Primero verificar si está vacío
        if (empty($email)) {
            throw new EmptyEmailException();
        }
        
        // Luego verificar formato
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }

        // En este método no se puede validar si el email existe o no, porque eso implicaría consulta a la base
        // de datos, es decir, que esta clase estaría acoplada al sistema de persistencia, y eso, en DDD y hexagonal, no puede pasar dentro del domain
    }

    public function value(): string
    {
        return $this->value;
    }
}