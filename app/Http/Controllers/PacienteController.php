<?php

namespace App\Http\Controllers;

use App\Models\ContactoEmergencia;
use App\Models\Paciente;
use App\Models\Perfilpaciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::with('usuario')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15);

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        $usuarios = $this->usuariosDisponibles();

        return view('pacientes.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $this->limpiarContactosVacios($request);
        $datos = $request->validate($this->reglasValidacion());
        $paciente = null;

        DB::transaction(function () use ($datos, $request, &$paciente): void {
            $datosPaciente = $this->datosPrincipales($datos, $request);
            $paciente = Paciente::create($datosPaciente);
            $this->guardarInformacionMedica($paciente, $datos['informacion_medica'] ?? []);
            $this->guardarContactos($paciente, $datos['contactos'] ?? []);
        });

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load(['usuario', 'informacionMedica', 'contactosEmergencia']);

        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        $paciente->load(['usuario', 'informacionMedica', 'contactosEmergencia']);
        $usuarios = $this->usuariosDisponibles($paciente);

        return view('pacientes.edit', compact('paciente', 'usuarios'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $this->limpiarContactosVacios($request);
        $datos = $request->validate($this->reglasValidacion($paciente));

        DB::transaction(function () use ($datos, $request, $paciente): void {
            $paciente->update($this->datosPrincipales($datos, $request));
            $this->guardarInformacionMedica($paciente, $datos['informacion_medica'] ?? []);
            $this->guardarContactos($paciente, $datos['contactos'] ?? []);
        });

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->estado = false;
        $paciente->save();

        return redirect()
            ->route('pacientes.index')
            ->with('success', 'Paciente desactivado correctamente.');
    }

    private function reglasValidacion(?Paciente $paciente = null): array
    {
        $rolPacienteId = Role::where('nombre', 'Paciente')->value('id_rol');

        $codigoUnico = Rule::unique('pacientes', 'codigo');
        $ciUnico = Rule::unique('pacientes', 'ci');
        $usuarioUnico = Rule::unique('pacientes', 'id_usuario');

        if ($paciente) {
            $codigoUnico->ignore($paciente->id_paciente, 'id_paciente');
            $ciUnico->ignore($paciente->id_paciente, 'id_paciente');
            $usuarioUnico->ignore($paciente->id_paciente, 'id_paciente');
        }

        return [
            'codigo' => ['required', 'string', 'max:12', $codigoUnico],
            'id_usuario' => [
                'nullable',
                'integer',
                Rule::exists('usuarios', 'id_usuario')
                    ->where('id_rol', $rolPacienteId)
                    ->where('estado', true),
                $usuarioUnico,
            ],
            'ci' => ['nullable', 'string', 'max:20', $ciUnico],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'sexo' => ['nullable', 'string', 'max:15'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'acepta_notificaciones' => ['required', 'boolean'],
            'acepta_privacidad' => ['required', 'boolean'],
            'estado' => ['required', 'boolean'],
            'informacion_medica' => ['nullable', 'array'],
            'informacion_medica.tipo_sangre' => ['nullable', 'string', 'max:5'],
            'informacion_medica.alergias' => ['nullable', 'string'],
            'informacion_medica.enfermedades_cronicas' => ['nullable', 'string'],
            'informacion_medica.medicamentos' => ['nullable', 'string'],
            'informacion_medica.antecedentes' => ['nullable', 'string'],
            'contactos' => ['nullable', 'array'],
            'contactos.*.id_contacto' => ['nullable', 'integer'],
            'contactos.*.nombre' => ['required', 'string', 'max:120'],
            'contactos.*.parentesco' => ['required', 'string', 'max:40'],
            'contactos.*.telefono' => ['required', 'string', 'max:20'],
        ];
    }

    private function datosPrincipales(array $datos, Request $request): array
    {
        return [
            'codigo' => $datos['codigo'],
            'id_usuario' => $datos['id_usuario'] ?? null,
            'ci' => $datos['ci'] ?? null,
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'fecha_nacimiento' => $datos['fecha_nacimiento'],
            'sexo' => $datos['sexo'] ?? null,
            'telefono' => $datos['telefono'],
            'correo' => $datos['correo'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'acepta_notificaciones' => $request->boolean('acepta_notificaciones'),
            'acepta_privacidad' => $request->boolean('acepta_privacidad'),
            'estado' => $request->boolean('estado'),
        ];
    }

    private function guardarInformacionMedica(Paciente $paciente, array $informacion): void
    {
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
    }

    private function guardarContactos(Paciente $paciente, array $contactos): void
    {
        $idsConservados = [];

        foreach ($contactos as $datosContacto) {
            $idContacto = $datosContacto['id_contacto'] ?? null;
            unset($datosContacto['id_contacto']);

            if ($idContacto) {
                $contacto = $paciente->contactosEmergencia()
                    ->where('id_contacto', $idContacto)
                    ->first();

                if (!$contacto) {
                    throw ValidationException::withMessages([
                        'contactos' => 'Uno de los contactos no pertenece a este paciente. Recarga el formulario e inténtalo nuevamente.',
                    ]);
                }

                $contacto->update($datosContacto);
            } else {
                $contacto = $paciente->contactosEmergencia()->create($datosContacto);
            }

            $idsConservados[] = $contacto->id_contacto;
        }

        $paciente->contactosEmergencia()
            ->whereNotIn('id_contacto', $idsConservados)
            ->delete();
    }

    private function limpiarContactosVacios(Request $request): void
    {
        $contactos = $request->input('contactos', []);

        if (!is_array($contactos)) {
            $request->merge(['contactos' => []]);
            return;
        }

        $contactos = array_values(array_filter($contactos, function ($contacto): bool {
            return is_array($contacto)
                && (
                    filled($contacto['nombre'] ?? null)
                    || filled($contacto['parentesco'] ?? null)
                    || filled($contacto['telefono'] ?? null)
                );
        }));

        $request->merge(['contactos' => $contactos]);
    }

    private function usuariosDisponibles(?Paciente $paciente = null)
    {
        $rolPacienteId = Role::where('nombre', 'Paciente')->value('id_rol');

        $usuariosAsignados = Paciente::query()
            ->whereNotNull('id_usuario')
            ->when(
                $paciente,
                fn ($consulta) => $consulta->where('id_paciente', '!=', $paciente->id_paciente)
            )
            ->select('id_usuario');

        return User::query()
            ->where('estado', true)
            ->where('id_rol', $rolPacienteId)
            ->whereNotIn('id_usuario', $usuariosAsignados)
            ->orderBy('nombre_completo')
            ->get();
    }
}
