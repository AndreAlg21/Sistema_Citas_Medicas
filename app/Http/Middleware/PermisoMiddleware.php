<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermisoMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $codigoPermiso
    ): Response {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $rol = $request->user()->role;

        if (
            !$rol ||
            !$rol->permisos()
                ->where('codigo', $codigoPermiso)
                ->exists()
        ) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}