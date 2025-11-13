<?php

declare(strict_types=1);

namespace Src\Auth\Application\Ports\Out;

use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\ValueObjects\UserEmail;

/**
 * Puerto de salida para la persistencia de usuarios.
 * Define el contrato que debe cumplir el repositorio.
 */
interface UserRepository
{
    /**
     * Guarda un nuevo usuario en el sistema de persistencia.
     *
     * @param User $user El usuario a guardar
     * @return void
     */
    public function save(User $user): void;

    /**
     * Verifica si existe un usuario con el email dado.
     *
     * @param UserEmail $email El email a verificar
     * @return bool True si existe, false en caso contrario
     */
    public function exists(UserEmail $email): bool;
}