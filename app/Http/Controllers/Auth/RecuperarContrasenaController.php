<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RecuperarContrasenaController extends Controller
{
    private const MINUTOS_VIGENCIA = 60;
    private const SEGUNDOS_ESPERA = 60;

    public function solicitar(): View
    {
        return view('auth.recuperar-contrasena');
    }

    public function enviarEnlace(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:120'],
        ]);

        $correo = mb_strtolower(trim($datos['correo']));
        $usuario = User::query()
            ->where('correo', $correo)
            ->where('estado', true)
            ->first();

        if ($usuario) {
            $tokenExistente = DB::table('tokens_recuperacion')
                ->where('correo', $correo)
                ->first();

            $puedeSolicitar = !$tokenExistente
                || Carbon::parse($tokenExistente->created_at)
                    ->addSeconds(self::SEGUNDOS_ESPERA)
                    ->isPast();

            if ($puedeSolicitar) {
                $token = Str::random(64);

                DB::table('tokens_recuperacion')->where('correo', $correo)->delete();
                DB::table('tokens_recuperacion')->insert([
                    'correo' => $correo,
                    'token_hash' => Hash::make($token),
                    'expira_en' => now()->addMinutes(self::MINUTOS_VIGENCIA),
                    'created_at' => now(),
                ]);

                $url = route('password.reset', [
                    'token' => $token,
                    'correo' => $correo,
                ]);

                try {
                    Mail::send('emails.restablecer-contrasena', [
                        'usuario' => $usuario,
                        'url' => $url,
                    ], function ($mensaje) use ($usuario): void {
                        $mensaje->to($usuario->correo, $usuario->nombre_completo)
                            ->subject('Recuperación de contraseña | Clínica Vida Salud');
                    });
                } catch (Throwable $error) {
                    Log::error('No se pudo enviar el enlace para recuperar la contraseña.', [
                        'id_usuario' => $usuario->id_usuario,
                        'error' => $error->getMessage(),
                    ]);
                }
            }
        }

        return back()->with(
            'status',
            'Si el correo pertenece a una cuenta activa, recibirás un enlace para cambiar la contraseña.'
        );
    }

    public function formularioRestablecer(Request $request, string $token): View|RedirectResponse
    {
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:120'],
        ]);

        $correo = mb_strtolower(trim($datos['correo']));
        if (!$this->tokenValido($correo, $token)) {
            return redirect()
                ->route('password.request')
                ->withErrors(['correo' => 'El enlace venció o ya fue utilizado. Solicita uno nuevo.']);
        }

        return view('auth.restablecer-contrasena', compact('correo', 'token'));
    }

    public function restablecer(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:120'],
            'token' => ['required', 'string', 'size:64'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $correo = mb_strtolower(trim($datos['correo']));
        if (!$this->tokenValido($correo, $datos['token'])) {
            throw ValidationException::withMessages([
                'correo' => 'El enlace venció o ya fue utilizado. Solicita uno nuevo.',
            ]);
        }

        $usuario = User::query()
            ->where('correo', $correo)
            ->where('estado', true)
            ->first();

        if (!$usuario) {
            throw ValidationException::withMessages([
                'correo' => 'No se pudo completar la recuperación. Solicita un enlace nuevo.',
            ]);
        }

        $usuario->update([
            'contrasena' => $datos['contrasena'],
            'intentos_fallidos' => 0,
            'bloqueado_hasta' => null,
        ]);
        DB::table('tokens_recuperacion')->where('correo', $correo)->delete();

        return redirect()
            ->route('login')
            ->with('success', 'Tu contraseña se cambió correctamente. Ya puedes iniciar sesión.');
    }

    private function tokenValido(string $correo, string $token): bool
    {
        $registro = DB::table('tokens_recuperacion')
            ->where('correo', $correo)
            ->first();

        return $registro !== null
            && Carbon::parse($registro->expira_en)->isFuture()
            && Hash::check($token, $registro->token_hash);
    }
}
