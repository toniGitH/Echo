<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User;

use Src\Auth\Domain\User\Exceptions\EmptyRolesCollectionException;
use Src\Auth\Domain\User\Exceptions\InvalidRoleTypeException;
use Src\Auth\Domain\User\ValueObjects\UserEmail;
use Src\Auth\Domain\User\ValueObjects\UserId;
use Src\Auth\Domain\User\ValueObjects\UserPassword;
use Src\Auth\Domain\User\ValueObjects\UserName;
use Src\Auth\Domain\User\ValueObjects\UserRole;

final class User
{
    private UserId $id;
    private UserName $name;
    private UserEmail $email;
    private ?UserPassword $password;
    
    /** @var UserRole[] */
    private array $roles = [];

    private function __construct(UserId $id, UserName $name, UserEmail $email, ?UserPassword $password = null, array $roles = []) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->setRoles($roles);
    }

    /**
     * Crea un nuevo usuario a partir de Value Objects ya validados.
     * 
     * @param UserName $name
     * @param UserEmail $email
     * @param UserPassword $password
     * @param UserRole[] $roles
     * @return self
     */
    public static function create(UserName $name, UserEmail $email, UserPassword $password, array $roles): self {
        
        $id = UserId::generate();
        return new self($id, $name, $email, $password, $roles);
    }

    /**
     * Reconstruye un usuario existente desde datos primitivos (por ejemplo, desde la BD).
     * No incluye la contraseña porque no es necesaria en el dominio después de la autenticación.
     * 
     * @param string $id
     * @param string $name
     * @param string $email
     * @param array $roles
     * @return self
     */
    public static function fromPrimitives(string $id, string $name, string $email, array $roles): self {
        
        $roleObjects = array_map(
            fn($role) => UserRole::fromString($role),
            $roles
        );
        
        // No incluimos password al reconstruir desde BD
        return new self(UserId::fromString($id), UserName::fromString($name), UserEmail::fromString($email), null, $roleObjects);
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

    private function setRoles(array $roles): void
    {
        $this->validateRoles($roles);
        $this->roles = $roles;
    }

    private function validateRoles(array $roles): void
    {
        if (empty($roles)) {
            throw new EmptyRolesCollectionException();
        }

        foreach ($roles as $role) {
            if (!$role instanceof UserRole) {
                throw new InvalidRoleTypeException();
            }
        }
    }

    /**
     * @return UserRole[]
     */
    public function roles(): array
    {
        return $this->roles;
    }

    public function hasRole(UserRole $role): bool
    {
        foreach ($this->roles as $userRole) {
            if ($userRole->equals($role)) {
                return true;
            }
        }
        return false;
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    public function addRole(UserRole $role): void
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(UserRole $role): void
    {
        $this->roles = array_filter(
            $this->roles,
            fn($r) => !$r->equals($role)
        );
        $this->roles = array_values($this->roles); // Reindex
    }

    public function isClient(): bool
    {
        return $this->hasRole(UserRole::client());
    }

    public function isFollower(): bool
    {
        return $this->hasRole(UserRole::follower());
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::admin());
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'email' => $this->email->value(),
            'roles' => array_map(fn($role) => $role->value(), $this->roles),
        ];

        // Solo incluir password si existe
        if ($this->password !== null) {
            $data['password'] = $this->password->value();
        }

        return $data;
    }
}