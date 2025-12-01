<?php

declare(strict_types=1);

namespace Src\Auth\Application\Ports\In;

use Src\Auth\Domain\User\User;

/**
 * Puerto de entrada para el caso de uso de login de usuario.
 * Define el contrato que debe cumplir el caso de uso.
 */
interface LoginUserPort
{
    /**
     * Autentica un usuario en el sistema.
     *
     * @param array $credentials Los datos de autenticación (email, password)
     * @return User El usuario autenticado
     * 
     * @throws \Src\Auth\Domain\User\Exceptions\InvalidCredentialsException Si las credenciales son inválidas
     * @throws \Src\Shared\Domain\Exceptions\MultipleDomainException Si hay múltiples errores de validación
     */
    public function execute(array $credentials): User;
}
