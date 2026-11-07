<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmacionRegistroPacienteMail;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Perfilpaciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PortalPacienteController extends Controller
{
    public function formularioRegistro(): View
    {
        return view('portal.registro');
    }

    public function registrar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'ci' => ['required', 'string', 'max:20'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo' => ['required', 'email', 'max:120', Rule::unique('usuarios', 'correo')],
            'acepta_privacidad' => ['accepted'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $rolPaciente = Role::query()->where('nombre', 'Paciente')->firstOrFail();

        $usuario = DB::transaction(function () use ($datos, $rolPaciente): User {
            $pacienteExistente = Paciente::query()
                ->where('ci', $datos['ci'])
                ->lockForUpdate()
                ->first();

            if ($pacienteExistente && $pacienteExistente->id_usuario) {
                throw ValidationException::withMessages([
                    'ci' => 'Ese documento ya está vinculado a una cuenta.',
                ]);
            }

            if ($pacienteExistente && !$pacienteExistente->estado) {
                throw ValidationException::withMessages([
                    'ci' => 'La ficha está inactiva. Contacta a recepción.',
                ]);
            }

            $nombreUsuario = $this->nombreUsuarioDisponible($datos['correo']);
            $usuario = User::create([
                'id_rol' => $rolPaciente->id_rol,
                'nombre_completo' => trim($datos['nombres'] . ' ' . $datos['apellidos']),
                'nombre_usuario' => $nombreUsuario,
                'correo' => $datos['correo'],
                'contrasena' => $datos['contrasena'],
                'estado' => true,
            ]);

            if ($pacienteExistente) {
                $pacienteExistente->update([
                    'id_usuario' => $usuario->id_usuario,
                    'correo' => $datos['correo'],
                    'telefono' => $datos['telefono'],
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'fecha_nacimiento' => $datos['fecha_nacimiento'],
                ]);
            } else {
                Paciente::create([
                    'codigo' => 'P-' . Str::upper(Str::random(10)),
                    'id_usuario' => $usuario->id_usuario,
                    'ci' => $datos['ci'],
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'fecha_nacimiento' => $datos['fecha_nacimiento'],
                    'telefono' => $datos['telefono'],
                    'correo' => $datos['correo'],
                    'acepta_notificaciones' => true,
                    'acepta_privacidad' => true,
                    'estado' => true,
                ]);
            }

            return $usuario;
        });

        try {
            Mail::to($usuario->correo)->send(new ConfirmacionRegistroPacienteMail($usuario));
        } catch (Throwable $error) {
            Log::warning('No se pudo enviar el correo de registro del paciente.', [
                'id_usuario' => $usuario->id_usuario,
                'error' => $error->getMessage(),
            ]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'La cuenta se creó correctamente. Revisa tu correo para ver la confirmación.');
    }

    public function perfil(Request $request): View
    {
        $paciente = $this->pacienteDelUsuario($request)
            ->load(['informacionMedica', 'contactosEmergencia']);

        return view('portal.perfil', compact('paciente'));
    }

    public function actualizarPerfil(Request $request): RedirectResponse
    {
        $paciente = $this->pacienteDelUsuario($request);
        $usuario = $request->user();

        $datos = $request->validate([
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'sexo' => ['nullable', 'string', 'max:15'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo' => [
                'required',
                'email',
                'max:120',
                Rule::unique('usuarios', 'correo')->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'direccion' => ['nullable', 'string', 'max:200'],
            'acepta_notificaciones' => ['required', 'boolean'],
            'acepta_privacidad' => ['required', 'boolean'],
            'informacion_medica' => ['nullable', 'array'],
            'informacion_medica.tipo_sangre' => ['nullable', 'string', 'max:5'],
            'informacion_medica.alergias' => ['nullable', 'string'],
            'informacion_medica.enfermedades_cronicas' => ['nullable', 'string'],
            'informacion_medica.medicamentos' => ['nullable', 'string'],
            'informacion_medica.antecedentes' => ['nullable', 'string'],
            'contacto.nombre' => ['nullable', 'required_with:contacto.parentesco,contacto.telefono', 'string', 'max:120'],
            'contacto.parentesco' => ['nullable', 'required_with:contacto.nombre,contacto.telefono', 'string', 'max:40'],
            'contacto.telefono' => ['nullable', 'required_with:contacto.nombre,contacto.parentesco', 'string', 'max:20'],
            'contrasena_actual' => ['nullable', 'required_with:nueva_contrasena', 'string'],
            'nueva_contrasena' => ['nullable', 'required_with:contrasena_actual', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($datos['nueva_contrasena'])) {
            if (!Hash::check($datos['contrasena_actual'], $usuario->contrasena)) {
                throw ValidationException::withMessages([
                    'contrasena_actual' => 'La contraseña actual no es correcta.',
                ]);
            }
        }

        DB::transaction(function () use ($paciente, $usuario, $datos): void {
            $paciente->update([
                'nombres' => $datos['nombres'],
                'apellidos' => $datos['apellidos'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'sexo' => $datos['sexo'] ?? null,
                'telefono' => $datos['telefono'],
                'correo' => $datos['correo'],
                'direccion' => $datos['direccion'] ?? null,
                'acepta_notificaciones' => $datos['acepta_notificaciones'],
                'acepta_privacidad' => $datos['acepta_privacidad'],
            ]);

            $usuario->update([
                'nombre_completo' => trim($datos['nombres'] . ' ' . $datos['apellidos']),
                'correo' => $datos['correo'],
                'telefono' => $datos['telefono'],
            ]);

            if (!empty($datos['nueva_contrasena'])) {
                $usuario->update(['contrasena' => $datos['nueva_contrasena']]);
            }

            $informacion = $datos['informacion_medica'] ?? [];
            Perfilpaciente::updateOrCreate(
                ['id_paciente' => $paciente->id_paciente],
                [
                    'tipo_sangre' => $informacion['tipo_sangre'] ?? null,
                    'alergias' => $informacion['alergias'] ?? null,
                    'enfermedades_cronicas' => $informacion['enfermedades_cronicas'] ?? null,
                    'medicamentos' => $informacion['medicamentos'] ?? null,
                    'antecedentes' => $informacion['antecedentes'] ?? null,
                ]
            );

            $contactoDatos = $datos['contacto'] ?? [];
            if (
                filled($contactoDatos['nombre'] ?? null)
                || filled($contactoDatos['parentesco'] ?? null)
                || filled($contactoDatos['telefono'] ?? null)
            ) {
                $contacto = $paciente->contactosEmergencia()->orderBy('id_contacto')->first();
                if ($contacto) {
                    $contacto->update([
                        'nombre' => $contactoDatos['nombre'] ?? '',
                        'parentesco' => $contactoDatos['parentesco'] ?? '',
                        'telefono' => $contactoDatos['telefono'] ?? '',
                    ]);
                } else {
                    $paciente->contactosEmergencia()->create([
                        'nombre' => $contactoDatos['nombre'] ?? '',
                        'parentesco' => $contactoDatos['parentesco'] ?? '',
                        'telefono' => $contactoDatos['telefono'] ?? '',
                    ]);
                }
            }
        });

        return back()->with('success', 'Tu perfil se actualizó correctamente.');
    }

    public function misCitas(Request $request): View
    {
        $paciente = $this->pacienteDelUsuario($request);
        $citas = Cita::query()
            ->with(['medico', 'especialidad'])
            ->where('id_paciente', $paciente->id_paciente)
            ->orderByDesc('fecha')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('estado');

        return view('portal.citas', compact('citas'));
    }

    public function miHistorialMedico(Request $request): View
    {
        $paciente = $this->pacienteDelUsuario($request)
            ->load('informacionMedica');

        $citasAtendidas = Cita::query()
            ->with(['medico', 'especialidad'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('estado', 'Atendida')
            ->orderByDesc('fecha')
            ->orderByDesc('hora_inicio')
            ->get();

        return view('portal.historial-medico', compact('paciente', 'citasAtendidas'));
    }

    private function pacienteDelUsuario(Request $request): Paciente
    {
        abort_unless($request->user()->role?->nombre === 'Paciente', 403);

        $paciente = Paciente::query()
            ->where('id_usuario', $request->user()->id_usuario)
            ->where('estado', true)
            ->first();

        abort_unless($paciente, 403, 'Tu usuario no está vinculado a una ficha activa de paciente.');

        return $paciente;
    }

    private function nombreUsuarioDisponible(string $correo): string
    {
        $base = Str::of(Str::before($correo, '@'))
            ->lower()
            ->replaceMatches('/[^a-z0-9_]/', '_')
            ->substr(0, 30)
            ->toString();

        $nombre = $base ?: 'paciente';
        $contador = 1;

        while (User::query()->where('nombre_usuario', $nombre)->exists()) {
            $sufijo = (string) $contador++;
            $nombre = substr($base ?: 'paciente', 0, 39 - strlen($sufijo)) . $sufijo;
        }

        return $nombre;
    }
}
