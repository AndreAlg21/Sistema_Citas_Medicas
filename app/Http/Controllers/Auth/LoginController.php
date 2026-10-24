<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'identificador' => ['required', 'string', 'max:120'],
            'contrasena' => ['required', 'string'],
        ], [
            'identificador.required' => 'Escribe tu correo o nombre de usuario.',
            'contrasena.required' => 'Escribe tu contraseña.',
        ]);

        $identificador = trim($datos['identificador']);
        $campo = filter_var($identificador, FILTER_VALIDATE_EMAIL)
            ? 'correo'
            : 'nombre_usuario';

        $usuario = User::query()->where($campo, $identificador)->first();

        if (!$usuario || !$usuario->estado) {
            return back()
                ->withErrors(['identificador' => 'Los datos ingresados no son correctos.'])
                ->onlyInput('identificador');
        }

        if ($usuario->bloqueado_hasta && $usuario->bloqueado_hasta->isPast()) {
            $usuario->forceFill([
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null,
            ])->save();
        }

        if ($usuario->bloqueado_hasta && $usuario->bloqueado_hasta->isFuture()) {
            return back()
                ->withErrors(['identificador' => 'La cuenta está bloqueada temporalmente. Intenta más tarde.'])
                ->onlyInput('identificador');
        }

        $credenciales = [
            $campo => $identificador,
            'password' => $datos['contrasena'],
            'estado' => true,
        ];

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            $usuario->forceFill([
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null,
                'ultimo_acceso' => now(),
            ])->save();

            return redirect()->intended(route('inicio'));
        }

        $intentos = (int) $usuario->intentos_fallidos + 1;
        $actualizacion = ['intentos_fallidos' => $intentos];

        if ($intentos >= 5) {
            $actualizacion['bloqueado_hasta'] = now()->addMinutes(15);
        }

        $usuario->forceFill($actualizacion)->save();

        $mensaje = $intentos >= 5
            ? 'La cuenta quedó bloqueada por 15 minutos.'
            : 'Los datos ingresados no son correctos.';

        return back()
            ->withErrors(['identificador' => $mensaje])
            ->onlyInput('identificador');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}