<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Persistence;

use Src\Auth\Application\Ports\Out\UserRepository;
use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\ValueObjects\UserEmail;
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
}