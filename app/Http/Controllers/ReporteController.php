<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->query->count() > 0
            ? $request->validate([
                'tipo' => ['nullable', Rule::in(['citas', 'pacientes'])],
                'fecha_desde' => ['nullable', 'date_format:Y-m-d'],
                'fecha_hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha_desde'],
                'id_medico' => ['nullable', 'integer', 'exists:medicos,id_medico'],
                'id_especialidad' => ['nullable', 'integer', 'exists:especialidades,id_especialidad'],
                'estado' => ['nullable', Rule::in(['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'Inasistencia'])],
            ])
            : [];

        $tipo = $filtros['tipo'] ?? 'citas';
        $desde = $filtros['fecha_desde'] ?? now()->startOfMonth()->toDateString();
        $hasta = $filtros['fecha_hasta'] ?? today()->toDateString();

        if ($tipo === 'pacientes') {
            abort_unless($request->user()->role?->nombre === 'Administrador', 403);

            $pacientes = Paciente::query()
                ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
                ->orderByDesc('created_at')
                ->get();
            $citas = collect();
            $totales = [
                'Total' => $pacientes->count(),
                'Activos' => $pacientes->where('estado', true)->count(),
                'Inactivos' => $pacientes->where('estado', false)->count(),
            ];

            return view('reportes.index', compact(
                'tipo',
                'desde',
                'hasta',
                'pacientes',
                'citas',
                'totales',
                'filtros'
            ));
        }

        $citas = Cita::query()
            ->with(['paciente', 'medico', 'especialidad'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->when($filtros['id_medico'] ?? null, fn ($consulta, $id) => $consulta->where('id_medico', $id))
            ->when($filtros['id_especialidad'] ?? null, fn ($consulta, $id) => $consulta->where('id_especialidad', $id))
            ->when($filtros['estado'] ?? null, fn ($consulta, $estado) => $consulta->where('estado', $estado))
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        $totales = $citas->groupBy('estado')->map->count();
        $totales['total'] = $citas->count();
        $pacientes = collect();
        $medicos = Medico::query()->where('estado', true)->orderBy('apellidos')->get();
        $especialidades = Especialidad::query()->where('estado', true)->orderBy('nombre')->get();

        return view('reportes.index', compact(
            'tipo',
            'desde',
            'hasta',
            'pacientes',
            'citas',
            'totales',
            'medicos',
            'especialidades',
            'filtros'
        ));
    }
}
