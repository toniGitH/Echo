<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainException;

/**
 * Excepción base para todas las validaciones de Value Objects del contexto Auth.
 * Permite capturar cualquier error de validación de datos de entrada.
 */
abstract class InvalidValueObjectException extends DomainException
{
}