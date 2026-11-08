<?php

namespace App\Http\Middleware;

use App\Models\Bitacora;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RegistrarBitacora
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuarioAntes = $request->user();
        $response = $next($request);
        $ruta = $request->route();
        $nombreRuta = $ruta?->getName() ?? 'ruta.sin_nombre';
        $usuario = $request->user() ?? $usuarioAntes ?? $this->usuarioDeSolicitud($request, $nombreRuta);
        [$tabla, $idRegistro] = $this->registroAfectado($ruta, $nombreRuta);

        $accion = match ($request->method()) {
            'GET', 'HEAD' => 'Consultó',
            'POST' => 'Ejecutó',
            'PUT', 'PATCH' => 'Actualizó',
            'DELETE' => 'Eliminó',
            default => 'Accedió',
        };

        if ($nombreRuta === 'login.store') {
            $accion = $request->user() ? 'Inició sesión' : 'Intentó iniciar sesión';
        } elseif ($nombreRuta === 'logout') {
            $accion = 'Cerró sesión';
        } elseif ($nombreRuta === 'password.email') {
            $accion = 'Solicitó recuperar contraseña';
        } elseif ($nombreRuta === 'password.update') {
            $accion = 'Restableció contraseña';
        } elseif ($nombreRuta === 'pacientes.registro.store' && $response->getStatusCode() < 400) {
            $accion = 'Registró cuenta de paciente';
        }

        Bitacora::query()->create([
            'tabla_afectada' => $tabla,
            'id_registro_afectado' => $idRegistro,
            'accion' => Str::limit($accion . ': ' . $nombreRuta, 50, ''),
            'valores_nuevos' => json_encode([
                'metodo' => $request->method(),
                'ruta' => $nombreRuta,
                'resultado_http' => $response->getStatusCode(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'fecha_hora' => now(),
            'id_usuario' => $usuario?->id_usuario,
            'direccion_ip' => $request->ip(),
        ]);

        return $response;
    }

    private function usuarioDeSolicitud(Request $request, string $nombreRuta): ?User
    {
        if ($nombreRuta === 'login.store') {
            $identificador = trim((string) $request->input('identificador', ''));

            return User::query()
                ->where('nombre_usuario', $identificador)
                ->orWhere('correo', $identificador)
                ->first();
        }

        if (in_array($nombreRuta, ['password.email', 'password.update', 'pacientes.registro.store'], true)) {
            return User::query()->where('correo', trim((string) $request->input('correo', '')))->first();
        }

        return null;
    }

    private function registroAfectado(mixed $ruta, string $nombreRuta): array
    {
        foreach ($ruta?->parameters() ?? [] as $parametro) {
            if ($parametro instanceof Model) {
                return [$parametro->getTable(), (int) $parametro->getKey()];
            }

            if (is_numeric($parametro)) {
                return [Str::before($nombreRuta, '.'), (int) $parametro];
            }
        }

        $modulo = Str::before($nombreRuta, '.');
        if ($modulo === 'portal') {
            $modulo = 'pacientes';
        } elseif ($modulo === 'password') {
            $modulo = 'usuarios';
        } elseif ($modulo === 'login' || $modulo === 'logout') {
            $modulo = 'usuarios';
        }

        return [Str::limit($modulo, 50, ''), 0];
    }
}
