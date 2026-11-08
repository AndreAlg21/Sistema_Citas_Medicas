<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
                'medico' => ['nullable', 'string', 'max:120'],
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

        $idMedico = $this->buscarMedicoPorIdentificador($filtros['medico'] ?? null);

        $citas = Cita::query()
            ->with(['paciente', 'medico', 'especialidad'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->when($idMedico, fn ($consulta, $id) => $consulta->where('id_medico', $id))
            ->when($filtros['id_especialidad'] ?? null, fn ($consulta, $id) => $consulta->where('id_especialidad', $id))
            ->when($filtros['estado'] ?? null, fn ($consulta, $estado) => $consulta->where('estado', $estado))
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        $totales = $citas->groupBy('estado')->map->count();
        $totales['total'] = $citas->count();
        $pacientes = collect();
        $especialidades = Especialidad::query()->where('estado', true)->orderBy('nombre')->get();

        return view('reportes.index', compact(
            'tipo',
            'desde',
            'hasta',
            'pacientes',
            'citas',
            'totales',
            'especialidades',
            'filtros'
        ));
    }

    private function buscarMedicoPorIdentificador(?string $busqueda): ?int
    {
        if (blank($busqueda)) {
            return null;
        }

        $busqueda = trim($busqueda);
        $palabras = preg_split('/\s+/', mb_strtolower($busqueda), -1, PREG_SPLIT_NO_EMPTY);
        $medico = Medico::query()->where('estado', true)
            ->where(function ($consulta) use ($busqueda, $palabras): void {
                $consulta->where('codigo', $busqueda);
                if (ctype_digit($busqueda)) {
                    $consulta->orWhere('id_medico', (int) $busqueda);
                }
                $consulta->orWhere(function ($nombres) use ($palabras): void {
                    foreach ($palabras as $palabra) {
                        $nombres->where(function ($coincidencia) use ($palabra): void {
                            $coincidencia->whereRaw('LOWER(nombres) LIKE ?', ['%' . $palabra . '%'])
                                ->orWhereRaw('LOWER(apellidos) LIKE ?', ['%' . $palabra . '%']);
                        });
                    }
                });
            })
            ->first(['id_medico']);

        if (!$medico) {
            throw ValidationException::withMessages([
                'medico' => 'No encontramos un médico con ese nombre, código o ID.',
            ]);
        }

        return (int) $medico->id_medico;
    }
}
