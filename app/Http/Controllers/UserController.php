<?php

namespace App\Http\Controllers;

use App\Models\ContactoEmergencia;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Perfilpaciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('role')
            ->orderBy('nombre_completo')
            ->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::where('estado', true)
            ->orderBy('nombre')
            ->get();
        $especialidades = \App\Models\Especialidad::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('usuarios.create', compact('roles', 'especialidades'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_rol' => ['required', Rule::exists('roles', 'id_rol')->where('estado', true)],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'nombre_usuario' => ['required', 'string', 'max:40', 'unique:usuarios,nombre_usuario'],
            'correo' => ['required', 'email', 'max:120', 'unique:usuarios,correo'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
            'telefono' => ['required', 'string', 'max:20'],
            'estado' => ['required', 'boolean'],
        ]);

        $rol = Role::findOrFail($datos['id_rol']);
        $reglasPerfil = [];

        if ($rol->nombre === 'Paciente') {
            $reglasPerfil = [
                'codigo' => ['required', 'string', 'max:12', 'unique:pacientes,codigo'],
                'ci' => ['required', 'string', 'max:20', 'unique:pacientes,ci'],
                'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
                'sexo' => ['nullable', 'string', 'max:15'],
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
            ];
        } elseif ($rol->nombre === 'Medico') {
            $reglasPerfil = [
                'codigo' => ['required', 'string', 'max:12', 'unique:medicos,codigo'],
                'registro_profesional' => ['required', 'string', 'max:30', 'unique:medicos,registro_profesional'],
                'especialidades' => ['required', 'array', 'min:1'],
                'especialidades.*' => [
                    'integer',
                    Rule::exists('especialidades', 'id_especialidad')->where('estado', true),
                ],
                'puede_marcar_atendida' => ['required', 'boolean'],
            ];
        }

        $datos += $request->validate($reglasPerfil);

        DB::transaction(function () use ($datos, $rol): void {
            $nombreCompleto = trim($datos['nombres'].' '.$datos['apellidos']);
            $usuario = User::create([
                'id_rol' => $rol->id_rol,
                'nombre_completo' => $nombreCompleto,
                'nombre_usuario' => $datos['nombre_usuario'],
                'correo' => $datos['correo'],
                'contrasena' => $datos['contrasena'],
                'telefono' => $datos['telefono'],
                'estado' => $datos['estado'],
            ]);

            if ($rol->nombre === 'Paciente') {
                $paciente = Paciente::create([
                    'codigo' => $datos['codigo'],
                    'id_usuario' => $usuario->id_usuario,
                    'ci' => $datos['ci'],
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'fecha_nacimiento' => $datos['fecha_nacimiento'],
                    'sexo' => $datos['sexo'] ?? null,
                    'telefono' => $datos['telefono'],
                    'correo' => $datos['correo'],
                    'direccion' => $datos['direccion'] ?? null,
                    'acepta_notificaciones' => $datos['acepta_notificaciones'],
                    'acepta_privacidad' => $datos['acepta_privacidad'],
                    'estado' => $datos['estado'],
                ]);

                $paciente->informacionMedica()->create($datos['informacion_medica'] ?? []);

                if (filled($datos['contacto']['nombre'] ?? null)) {
                    $paciente->contactosEmergencia()->create($datos['contacto']);
                }
            } elseif ($rol->nombre === 'Medico') {
                $medico = Medico::create([
                    'codigo' => $datos['codigo'],
                    'id_usuario' => $usuario->id_usuario,
                    'registro_profesional' => $datos['registro_profesional'],
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'telefono' => $datos['telefono'],
                    'correo' => $datos['correo'],
                    'puede_marcar_atendida' => $datos['puede_marcar_atendida'],
                    'estado' => $datos['estado'],
                ]);

                $medico->especialidades()->sync($datos['especialidades']);
            }
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'La cuenta y los datos del perfil se registraron correctamente.');
    }

    public function show(string $id)
    {
        $usuario = User::with('role')->findOrFail($id);

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(string $id)
    {
        $usuario = User::findOrFail($id);

        $roles = Role::where('estado', true)
            ->orWhere('id_rol', $usuario->id_rol)
            ->orderBy('nombre')
            ->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        $datos = $request->validate([
            'id_rol' => ['required', 'exists:roles,id_rol'],
            'nombre_completo' => ['required', 'string', 'max:120'],
            'nombre_usuario' => [
                'required',
                'string',
                'max:40',
                Rule::unique('usuarios', 'nombre_usuario')
                    ->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'correo' => [
                'required',
                'email',
                'max:120',
                Rule::unique('usuarios', 'correo')
                    ->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'contrasena' => ['nullable', 'string', 'min:8', 'confirmed'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'estado' => ['sometimes', 'boolean'],
        ]);

        if (empty($datos['contrasena'])) {
            unset($datos['contrasena']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'El usuario se actualizó correctamente.');
    }

    public function destroy(string $id)
    {
        $usuario = User::findOrFail($id);

        if ((int) $usuario->id_usuario === (int) auth()->id()) {
            return back()->withErrors([
                'usuario' => 'No puedes desactivar tu propia cuenta.',
            ]);
        }

        $usuario->update(['estado' => false]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'El usuario se desactivó correctamente.');
    }
}