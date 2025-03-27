<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Garante que o usuário autenticado tem uma empresa associada
            if (! $user->company_id) {
                return response()->json(['message' => 'Unauthorized - No company assigned'], 403);
            }

            // Impede que o usuário acesse outra empresa
            if ($request->has('company_id') && $request->company_id != $user->company_id) {
                return response()->json(['message' => 'Unauthorized - Invalid company access'], 403);
            }

            // Adiciona automaticamente a company_id ao request (caso necessário)
            $request->merge(['company_id' => $user->company_id]);

            return $next($request);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not valid'], 403);
        }
        // Registra o usuário autenticado no guard 'api'
        Auth::login($user); // Aqui fazemos o login explícito do usuário autenticado

        return $next($request);
    }
}
