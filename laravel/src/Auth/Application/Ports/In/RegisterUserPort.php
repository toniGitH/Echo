<?php

declare(strict_types=1);

namespace Src\Auth\Application\Ports\In;

use Src\Auth\Domain\User\User;

/**
 * Puerto de entrada para el caso de uso de registro de usuario.
 * Define el contrato que debe cumplir el caso de uso.
 */
interface RegisterUserPort
{
    /**
     * Registra un nuevo usuario en el sistema.
     *
     * @param array $userData Los datos del usuario a registrar (name, email, password)
     * @return User El usuario registrado
     * 
     * @throws \Src\Auth\Domain\User\Exceptions\InvalidEmailException Si el email no es válido
     * @throws \Src\Auth\Domain\User\Exceptions\InvalidPasswordException Si la contraseña no cumple requisitos
     * @throws \Src\Auth\Domain\User\Exceptions\MissingUserNameException Si el nombre está vacío
     * @throws \Src\Auth\Domain\User\Exceptions\MultipleValidationErrorsException Si hay múltiples errores de validación
     * @throws \Src\Auth\Domain\User\Exceptions\EmailAlreadyExistsException Si el email ya existe
     */
    public function execute(array $userData): User;
}