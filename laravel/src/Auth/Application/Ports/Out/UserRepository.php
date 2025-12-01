<?php

declare(strict_types=1);

namespace Src\Auth\Application\Ports\Out;

use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\ValueObjects\UserEmail;
use Src\Auth\Domain\User\ValueObjects\UserPassword;

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

    /**
     * Busca un usuario por su email.
     *
     * @param UserEmail $email El email del usuario a buscar
     * @return User|null El usuario si existe, null en caso contrario
     */
    public function findByEmail(UserEmail $email): ?User;

    /**
     * Busca un usuario por sus credenciales (email y contraseña).
     * Verifica que la contraseña coincida con el hash almacenado.
     *
     * @param UserEmail $email El email del usuario
     * @param UserPassword $password La contraseña en texto plano
     * @return User|null El usuario si las credenciales son válidas, null en caso contrario
     */
    public function findByCredentials(UserEmail $email, UserPassword $password): ?User;
}