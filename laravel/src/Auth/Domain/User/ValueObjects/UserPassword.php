<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\ValueObjects;

use Src\Auth\Domain\User\Exceptions\EmptyPasswordException;
use Src\Auth\Domain\User\Exceptions\InvalidPasswordException;

/**
 * Value Object para la contraseña del usuario.
 * 
 * NOTA: Este Value Object NO implementa equals() a diferencia de otros VOs (UserId, UserName, UserEmail).
 * Aunque esto supone una inconsistencia, es una excepción justificada porque:
 * 
 * 1. Las contraseñas se almacenan hasheadas (bcrypt) en la base de datos.
 * 2. Es IMPOSIBLE obtener la contraseña original a partir del hash (función irreversible).
 * 3. La comparación de contraseñas se realiza con Hash::check() en la capa de infraestructura.
 * 4. Este Value Object solo maneja contraseñas en texto plano (antes de hashear).
 * 
 * Por tanto, no existe un escenario donde comparar dos UserPassword tenga sentido práctico.
 */
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