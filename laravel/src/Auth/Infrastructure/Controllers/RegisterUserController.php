<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Controllers;

use App\Http\Requests\Auth\RegisterUserRequest;
use Src\Auth\Application\Ports\In\RegisterUserPort;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Controlador para el registro de nuevos usuarios.
 *
 * No captura excepciones de dominio: las deja subir para que el Handler global las normalice.
 */
final class RegisterUserController extends Controller
{
    private readonly RegisterUserPort $registerUserPort;

    public function __construct(RegisterUserPort $registerUserPort)
    {
        $this->registerUserPort = $registerUserPort;
    }

    /**
     * Invocable controller para registrar un usuario.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function __invoke(RegisterUserRequest $request): JsonResponse
    {
        // Obtener datos validados (array con name, email, password, password_confirmation)
        $validatedData = $request->validated();

        // Llamada al puerto del caso de uso (Puerto de entrada).
        $user = $this->registerUserPort->execute($validatedData);

        return new JsonResponse([
            'message' => __('messages.user.registered_success'),
            'user' => [
                'id' => $user->id()->value(),
                'name' => $user->name()->value(),
                'email' => $user->email()->value(),
            ]
        ], 201);
    }
}
