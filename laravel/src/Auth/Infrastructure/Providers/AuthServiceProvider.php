<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Providers;

use Src\Auth\Application\Ports\In\RegisterUserPort;
use Src\Auth\Application\Ports\In\LoginUserPort;
use Src\Auth\Application\Ports\Out\UserRepository;
use Src\Auth\Application\UseCases\LoginUserUseCase;
use Src\Auth\Application\UseCases\RegisterUserUseCase;
use Src\Auth\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider del contexto Auth.
 * Registra todas las dependencias e implementaciones del contexto.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Registra los bindings del contenedor de dependencias.
     */
    public function register(): void
    {
        // Registrar implementación del repositorio
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        
        // Registrar implementación de los casos de uso
        $this->app->bind(RegisterUserPort::class, RegisterUserUseCase::class);
        $this->app->bind(LoginUserPort::class, LoginUserUseCase::class);

    }

    /**
     * Bootstrap de servicios del contexto Auth.
     */
    public function boot(): void
    {
        //
    }
}