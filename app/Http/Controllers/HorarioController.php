<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Especialidad;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::with(['medico', 'especialidad'])
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

        $especialidades = Especialidad::where('estado', true)->orderBy('nombre')->get();

        return view('horarios.create', compact('medicos', 'especialidades'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglasValidacion());
        $this->validarHorarioClinica($datos);
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
        $horario->load(['medico', 'especialidad']);

        return view('horarios.show', compact('horario'));
    }

    public function edit(Horario $horario)
    {
        $medicos = Medico::where('estado', true)
            ->orWhere('id_medico', $horario->id_medico)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
        $especialidades = Especialidad::where('estado', true)->orderBy('nombre')->get();

        return view('horarios.edit', compact('horario', 'medicos', 'especialidades'));
    }

    public function update(Request $request, Horario $horario)
    {
        $datos = $request->validate($this->reglasValidacion($horario));
        $this->validarHorarioClinica($datos);
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
            'id_especialidad' => [
                'required',
                'integer',
                Rule::exists('especialidades', 'id_especialidad')->where('estado', true),
            ],
            'dia_semana' => ['required', 'integer', 'between:1,5'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'duracion_cita_min' => ['required', 'integer', 'in:15'],
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

    private function validarHorarioClinica(array $datos): void
    {
        $medicoAtiendeEspecialidad = Medico::query()
            ->whereKey($datos['id_medico'])
            ->whereHas(
                'especialidades',
                fn ($consulta) => $consulta->where(
                    'especialidades.id_especialidad',
                    $datos['id_especialidad']
                )
            )
            ->exists();

        if (!$medicoAtiendeEspecialidad) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'id_especialidad' => 'El médico debe tener asignada esta especialidad.',
            ]);
        }

        $inicio = substr($datos['hora_inicio'], 0, 5);
        $fin = substr($datos['hora_fin'], 0, 5);
        $minutos = (int) substr($inicio, 3, 2);
        $enManana = $inicio >= '07:00' && $inicio < '12:00' && $fin <= '12:00';
        $enTarde = $inicio >= '14:00' && $inicio < '18:00' && $fin <= '18:00';

        if (!$enManana && !$enTarde) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hora_inicio' => 'El horario debe estar dentro de 07:00–12:00 o 14:00–18:00.',
            ]);
        }

        if ($minutos % 15 !== 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hora_inicio' => 'El inicio del horario debe coincidir con un intervalo de 15 minutos.',
            ]);
        }
    }
}

