<?php

declare(strict_types=1);

namespace Src\Auth\Application\UseCases;

use Src\Auth\Application\Ports\In\LoginUserPort;
use Src\Auth\Application\Ports\Out\UserRepository;
use Src\Auth\Domain\User\User;
use Src\Auth\Domain\User\ValueObjects\UserEmail;
use Src\Auth\Domain\User\ValueObjects\UserPassword;
use Src\Auth\Domain\User\Exceptions\InvalidCredentialsException;
use Src\Shared\Domain\Exceptions\InvalidValueObjectException;
use Src\Shared\Domain\Exceptions\MultipleDomainException;

final class LoginUserUseCase implements LoginUserPort
{
    private readonly UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(array $credentials): User
    {
        $errors = [];

        // Crear Value Objects y acumular errores
        try {
            $email = UserEmail::fromString($credentials['email'] ?? '');
        } catch (InvalidValueObjectException $e) {
            $errors['email'][] = $e->getMessage();
        }

        try {
            $password = UserPassword::fromString($credentials['password'] ?? '');
        } catch (InvalidValueObjectException $e) {
            $errors['password'][] = $e->getMessage();
        }

        // Si hay errores de validación, lanzamos excepción compuesta
        if (!empty($errors)) {
            throw new MultipleDomainException($errors);
        }

        // Verificar credenciales directamente
        $user = $this->userRepository->findByCredentials($email, $password);

        // Si las credenciales no son válidas, lanzar excepción
        if ($user === null) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }
}
