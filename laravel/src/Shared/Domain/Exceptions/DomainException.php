<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Exception;

/**
 * Excepción base para todas las excepciones de dominio de la aplicación.
 * Todas las excepciones de negocio deben heredar de esta clase.
 */
abstract class DomainException extends Exception
{
}