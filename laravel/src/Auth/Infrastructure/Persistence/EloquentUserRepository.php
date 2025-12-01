<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Persistence;

use Src\Auth\Application\Ports\Out\UserRepository;
use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\ValueObjects\UserEmail;
use Src\Auth\Domain\User\ValueObjects\UserPassword;
use App\Models\User as EloquentUser;
use Illuminate\Support\Facades\Hash;

/**
 * Implementación del repositorio de usuarios usando Eloquent.
 * Actúa como adaptador entre el dominio y la base de datos.
 */
final class EloquentUserRepository implements UserRepository
{
    public function save(User $user): void
    {
        $eloquentUser = new EloquentUser();
        $eloquentUser->id = $user->id()->value();
        $eloquentUser->name = $user->name()->value();
        $eloquentUser->email = $user->email()->value();
        // Hasheamos la contraseña explícitamente antes de guardar
        $eloquentUser->password = Hash::make($user->password()->value());
        $eloquentUser->save();
    }

    public function exists(UserEmail $email): bool
    {
        return EloquentUser::where('email', $email->value())->exists();
    }

    public function findByEmail(UserEmail $email): ?User
    {
        $eloquentUser = EloquentUser::where('email', $email->value())->first();

        if ($eloquentUser === null) {
            return null;
        }

        // Reconstruir la entidad de dominio sin password
        return User::fromPrimitives(
            $eloquentUser->id,
            $eloquentUser->name,
            $eloquentUser->email
        );
    }

    public function findByCredentials(UserEmail $email, UserPassword $password): ?User
    {
        $eloquentUser = EloquentUser::where('email', $email->value())->first();

        // Si no existe el usuario o la contraseña no coincide, retornar null
        if ($eloquentUser === null || !Hash::check($password->value(), $eloquentUser->password)) {
            return null;
        }

        // Solo reconstruir el User de dominio si las credenciales son válidas
        return User::fromPrimitives(
            $eloquentUser->id,
            $eloquentUser->name,
            $eloquentUser->email
        );
    }
}