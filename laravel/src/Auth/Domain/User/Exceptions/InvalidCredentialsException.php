<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\Exceptions;

use Src\Shared\Domain\Exceptions\DomainException;

/**
 * Excepción lanzada cuando las credenciales de login son inválidas.
 * Esto incluye tanto email no existente como contraseña incorrecta.
 * Por seguridad, no se especifica cuál de los dos es el problema.
 */
final class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('messages.user.INVALID_CREDENTIALS');
    }

    public function errorCode(): string
    {
        return 'INVALID_CREDENTIALS';
    }
}
