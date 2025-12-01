<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User;

use Src\Auth\Domain\User\ValueObjects\UserEmail;
use Src\Auth\Domain\User\ValueObjects\UserId;
use Src\Auth\Domain\User\ValueObjects\UserPassword;
use Src\Auth\Domain\User\ValueObjects\UserName;

final class User
{
    private function __construct(
        private UserId $id,
        private UserName $name,
        private UserEmail $email,
        private ?UserPassword $password = null
    ) {}

    /**
     * Crea un nuevo usuario a partir de Value Objects ya validados.
     * 
     * @param UserName $name
     * @param UserEmail $email
     * @param UserPassword $password
     * @return self
     */
    public static function create(
        UserName $name,
        UserEmail $email,
        UserPassword $password
    ): self {
        $id = UserId::generate();
        
        return new self($id, $name, $email, $password);
    }

    /**
     * Reconstruye un usuario existente desde datos primitivos (por ejemplo, desde la BD).
     * No incluye la contraseña porque no es necesaria en el dominio después de la autenticación.
     * 
     * @param string $id
     * @param string $name
     * @param string $email
     * @return self
     */
    public static function fromPrimitives(
        string $id,
        string $name,
        string $email
    ): self {
        return new self(
            UserId::fromString($id),
            UserName::fromString($name),
            UserEmail::fromString($email),
            null // No incluimos password al reconstruir desde BD
        );
    }


    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function nameValue(): string
    {
        return $this->name->value();
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function password(): ?UserPassword
    {
        return $this->password;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'email' => $this->email->value(),
        ];

        // Solo incluir password si existe
        if ($this->password !== null) {
            $data['password'] = $this->password->value();
        }

        return $data;
    }
}