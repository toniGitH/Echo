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
        private UserPassword $password
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

    public function password(): UserPassword
    {
        return $this->password;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'email' => $this->email->value(),
            'password' => $this->password->value()
        ];
    }
}