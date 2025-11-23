<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Controllers;

use App\Http\Requests\Auth\LoginUserRequest;
use App\Models\User as EloquentUser;
use Src\Auth\Application\Ports\In\LoginUserPort;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Controlador para el login de usuarios.
 *
 * No captura excepciones de dominio: las deja subir para que el Handler global las normalice.
 */
final class LoginUserController extends Controller
{
    private readonly LoginUserPort $loginUserPort;

    public function __construct(LoginUserPort $loginUserPort)
    {
        $this->loginUserPort = $loginUserPort;
    }

    /**
     * Invocable controller para autenticar un usuario usando Tokens (API Mode).
     *
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function __invoke(LoginUserRequest $request): JsonResponse
    {
        // Obtener datos validados (array con email, password)
        $validatedData = $request->validated();

        // Llamada al puerto del caso de uso (Puerto de entrada)
        // Esto verifica credenciales y devuelve el usuario de dominio
        $user = $this->loginUserPort->execute($validatedData);

        // Buscar el modelo Eloquent (necesario para generar el token)
        $eloquentUser = EloquentUser::where('email', $user->email()->value())->first();

        // Generar token de acceso (API Authentication)
        // El token se usa para autenticar peticiones posteriores
        $token = $eloquentUser->createToken('api-token')->plainTextToken;

        return new JsonResponse([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => [
                'id' => $user->id()->value(),
                'name' => $user->name()->value(),
                'email' => $user->email()->value(),
            ]
        ], 200);
    }
}
