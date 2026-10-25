<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicoController extends Controller
{
    public function index()
    {
        $medicos = Medico::with(['usuario', 'especialidades'])
            ->orderBy('apellidos')
            ->paginate(10);

        return view('medicos.index', compact('medicos'));
    }

    public function create()
    {
        $especialidades = Especialidad::where('estado', true)
            ->orderBy('nombre')
            ->get();
        $usuarios = $this->usuariosDisponibles();

        return view('medicos.create', compact('especialidades', 'usuarios'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglasValidacion());
        $idsEspecialidades = $datos['especialidades'];
        unset($datos['especialidades']);

        $datos['id_usuario'] = $datos['id_usuario'] ?? null;
        $datos['puede_marcar_atendida'] = $request->boolean('puede_marcar_atendida');
        $datos['estado'] = $request->boolean('estado');

        $medico = Medico::create($datos);
        $medico->especialidades()->sync($idsEspecialidades);

        return redirect()
            ->route('medicos.index')
            ->with('success', 'Médico registrado correctamente.');
    }

    public function show(Medico $medico)
    {
        $medico->load(['usuario', 'especialidades']);

        return view('medicos.show', compact('medico'));
    }

    public function edit(Medico $medico)
    {
        $medico->load(['usuario', 'especialidades']);
        $especialidades = Especialidad::where('estado', true)
            ->orderBy('nombre')
            ->get();
        $usuarios = $this->usuariosDisponibles($medico);

        return view('medicos.edit', compact('medico', 'especialidades', 'usuarios'));
    }

    public function update(Request $request, Medico $medico)
    {
        $datos = $request->validate($this->reglasValidacion($medico));
        $idsEspecialidades = $datos['especialidades'];
        unset($datos['especialidades']);

        $datos['id_usuario'] = $datos['id_usuario'] ?? null;
        $datos['puede_marcar_atendida'] = $request->boolean('puede_marcar_atendida');
        $datos['estado'] = $request->boolean('estado');

        $medico->update($datos);
        $medico->especialidades()->sync($idsEspecialidades);

        return redirect()
            ->route('medicos.index')
            ->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(Medico $medico)
    {
        $medico->estado = false;
        $medico->save();

        return redirect()
            ->route('medicos.index')
            ->with('success', 'Médico desactivado correctamente.');
    }

    private function usuariosDisponibles(?Medico $medico = null)
    {
        $rolMedicoId = Role::where('nombre', 'Medico')->value('id_rol');

        $usuariosAsignados = Medico::query()
            ->whereNotNull('id_usuario')
            ->when(
                $medico,
                fn ($consulta) => $consulta->where('id_medico', '!=', $medico->id_medico)
            )
            ->select('id_usuario');

        return User::query()
            ->where('estado', true)
            ->where('id_rol', $rolMedicoId)
            ->whereNotIn('id_usuario', $usuariosAsignados)
            ->orderBy('nombre_completo')
            ->get();
    }

    private function reglasValidacion(?Medico $medico = null): array
    {
        $rolMedicoId = Role::where('nombre', 'Medico')->value('id_rol');

        $codigoUnico = Rule::unique('medicos', 'codigo');
        $registroUnico = Rule::unique('medicos', 'registro_profesional');
        $usuarioUnico = Rule::unique('medicos', 'id_usuario');

        if ($medico) {
            $codigoUnico->ignore($medico->id_medico, 'id_medico');
            $registroUnico->ignore($medico->id_medico, 'id_medico');
            $usuarioUnico->ignore($medico->id_medico, 'id_medico');
        }

        return [
            'codigo' => ['required', 'string', 'max:12', $codigoUnico],
            'id_usuario' => [
                'nullable',
                'integer',
                Rule::exists('usuarios', 'id_usuario')
                    ->where('id_rol', $rolMedicoId)
                    ->where('estado', true),
                $usuarioUnico,
            ],
            'registro_profesional' => ['required', 'string', 'max:30', $registroUnico],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo' => ['required', 'email', 'max:120'],
            'puede_marcar_atendida' => ['required', 'boolean'],
            'estado' => ['required', 'boolean'],
            'especialidades' => ['required', 'array', 'min:1'],
            'especialidades.*' => [
                'integer',
                Rule::exists('especialidades', 'id_especialidad')
                    ->where('estado', true),
            ],
        ];
    }
}
