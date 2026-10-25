<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::with('medico')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->paginate(15);

        return view('horarios.index', compact('horarios'));
    }

    public function create()
    {
        $medicos = Medico::where('estado', true)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        return view('horarios.create', compact('medicos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglasValidacion());
        $datos['estado'] = $request->boolean('estado');

        if ($this->existeChoque($datos)) {
            return back()
                ->withErrors(['hora_inicio' => 'Ese médico ya tiene un horario que se cruza con el horario indicado para ese día.'])
                ->withInput();
        }

        Horario::create($datos);

        return redirect()
            ->route('horarios.index')
            ->with('success', 'Horario registrado correctamente.');
    }

    public function show(Horario $horario)
    {
        $horario->load('medico');

        return view('horarios.show', compact('horario'));
    }

    public function edit(Horario $horario)
    {
        $medicos = Medico::where('estado', true)
            ->orWhere('id_medico', $horario->id_medico)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        return view('horarios.edit', compact('horario', 'medicos'));
    }

    public function update(Request $request, Horario $horario)
    {
        $datos = $request->validate($this->reglasValidacion($horario));
        $datos['estado'] = $request->boolean('estado');

        if ($this->existeChoque($datos, $horario)) {
            return back()
                ->withErrors(['hora_inicio' => 'Ese médico ya tiene otro horario que se cruza con el horario indicado para ese día.'])
                ->withInput();
        }

        $horario->update($datos);

        return redirect()
            ->route('horarios.index')
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Horario $horario)
    {
        $horario->estado = false;
        $horario->save();

        return redirect()
            ->route('horarios.index')
            ->with('success', 'Horario desactivado correctamente.');
    }

    private function reglasValidacion(?Horario $horario = null): array
    {
        return [
            'id_medico' => [
                'required',
                'integer',
                Rule::exists('medicos', 'id_medico')->where(function ($consulta) use ($horario): void {
                    $consulta->where('estado', true);

                    if ($horario) {
                        $consulta->orWhere('id_medico', $horario->id_medico);
                    }
                }),
            ],
            'dia_semana' => ['required', 'integer', 'between:1,7'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'duracion_cita_min' => ['required', 'integer', 'between:5,240'],
            'consultorio' => ['required', 'string', 'max:40'],
            'estado' => ['required', 'boolean'],
        ];
    }

    private function existeChoque(array $datos, ?Horario $horario = null): bool
    {
        return Horario::query()
            ->where('id_medico', $datos['id_medico'])
            ->where('dia_semana', $datos['dia_semana'])
            ->where('estado', true)
            ->when(
                $horario,
                fn ($consulta) => $consulta->where('id_horario', '!=', $horario->id_horario)
            )
            ->where('hora_inicio', '<', $datos['hora_fin'])
            ->where('hora_fin', '>', $datos['hora_inicio'])
            ->exists()
            && (bool) $datos['estado'];
    }
}

