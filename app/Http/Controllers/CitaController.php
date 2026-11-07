<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmacionCitaMail;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\HistorialCita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\ParametroSistema;
use App\Services\DisponibilidadCitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CitaController extends Controller
{
    public function __construct(private readonly DisponibilidadCitasService $disponibilidad)
    {
    }

    public function disponibilidad(Request $request): View
    {
        abort_unless(
            in_array($request->user()->role?->nombre, ['Recepcionista', 'Paciente'], true),
            403
        );

        $filtros = $request->query->count() > 0
            ? $this->validarFiltros($request)
            : [];

        $especialidades = Especialidad::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $medicos = Medico::query()
            ->with('especialidades')
            ->where('estado', true)
            ->when(
                $filtros['id_especialidad'] ?? null,
                fn ($consulta, $id) => $consulta->whereHas(
                    'especialidades',
                    fn ($especialidades) => $especialidades->where(
                        'especialidades.id_especialidad',
                        $id
                    )
                )
            )
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $opciones = $filtros === []
            ? []
            : $this->disponibilidad->buscar(
                (int) $filtros['id_especialidad'],
                isset($filtros['id_medico']) ? (int) $filtros['id_medico'] : null,
                $filtros['fecha'],
                null,
                $filtros['consultorio'] ?? null
            );

        $sugerencias = [];
        if ($filtros !== [] && $opciones === []) {
            $fechaBase = Carbon::parse($filtros['fecha']);
            for ($dia = 1; $dia <= 13 && count($sugerencias) < 3; $dia++) {
                $fechaAlternativa = $fechaBase->copy()->addDays($dia)->toDateString();
                if (Carbon::parse($fechaAlternativa)->greaterThan(today()->addDays(13))) {
                    break;
                }
                $alternativas = $this->disponibilidad->buscar(
                    (int) $filtros['id_especialidad'],
                    isset($filtros['id_medico']) ? (int) $filtros['id_medico'] : null,
                    $fechaAlternativa,
                    null,
                    $filtros['consultorio'] ?? null
                );
                if ($alternativas !== []) {
                    $sugerencias[] = $fechaAlternativa;
                }
            }
        }

        return view('citas.disponibilidad', compact(
            'especialidades',
            'medicos',
            'filtros',
            'opciones',
            'sugerencias'
        ));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->role?->nombre === 'Recepcionista', 403);

        $request->validate(['ci' => ['nullable', 'string', 'max:20']]);
        $ci = trim((string) $request->query('ci', ''));
        $pacienteSeleccionado = $ci === ''
            ? null
            : Paciente::query()->where('ci', $ci)->where('estado', true)->first();
        $pacienteNoEncontrado = $ci !== '' && !$pacienteSeleccionado;

        $filtros = $request->filled('id_especialidad') || $request->filled('fecha')
            ? $this->validarFiltros($request)
            : [];

        $especialidades = Especialidad::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $medicos = Medico::query()
            ->with('especialidades')
            ->where('estado', true)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $opciones = $filtros === []
            ? []
            : $this->disponibilidad->buscar(
                (int) $filtros['id_especialidad'],
                isset($filtros['id_medico']) ? (int) $filtros['id_medico'] : null,
                $filtros['fecha'],
                null,
                $filtros['consultorio'] ?? null
            );

        return view('citas.create', compact(
            'especialidades',
            'medicos',
            'filtros',
            'opciones',
            'ci',
            'pacienteSeleccionado',
            'pacienteNoEncontrado'
        ));
    }

    public function buscarPacientePorCi(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'ci' => ['required', 'string', 'max:20'],
        ]);

        $paciente = Paciente::query()
            ->where('ci', trim($datos['ci']))
            ->where('estado', true)
            ->first(['id_paciente', 'codigo', 'ci', 'nombres', 'apellidos']);

        if (!$paciente) {
            return response()->json(['mensaje' => 'No encontramos una ficha activa con ese carnet.'], 404);
        }

        return response()->json([
            'paciente' => [
                'codigo' => $paciente->codigo,
                'ci' => $paciente->ci,
                'nombres' => $paciente->nombres,
                'apellidos' => $paciente->apellidos,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role?->nombre === 'Recepcionista', 403);

        $datos = $request->validate([
            'ci_paciente' => [
                'required',
                'string',
                Rule::exists('pacientes', 'ci')->where('estado', true),
            ],
            ...$this->reglasReserva(),
        ], $this->mensajesFechaReserva());

        $paciente = Paciente::query()
            ->where('ci', $datos['ci_paciente'])
            ->where('estado', true)
            ->firstOrFail();

        $cita = $this->crearCita(
            $datos,
            (int) $paciente->id_paciente,
            (int) $request->user()->id_usuario
        );

        return redirect()
            ->route('citas.show', $cita)
            ->with('success', 'La cita se registró correctamente.');
    }

    public function reservarEnLinea(Request $request): RedirectResponse
    {
        $usuario = $request->user();
        abort_unless($usuario->role?->nombre === 'Paciente', 403);

        $paciente = Paciente::query()
            ->where('id_usuario', $usuario->id_usuario)
            ->where('estado', true)
            ->firstOrFail();

        $datos = $request->validate($this->reglasReserva(), $this->mensajesFechaReserva());
        $maxPendientes = $this->parametroEntero('max_citas_pendientes_paciente', 3);
        $pendientes = Cita::query()
            ->where('id_paciente', $paciente->id_paciente)
            ->whereIn('estado', ['Programada', 'Confirmada'])
            ->whereDate('fecha', '>=', today())
            ->count();

        if ($pendientes >= $maxPendientes) {
            throw ValidationException::withMessages([
                'hora_inicio' => "Ya tienes el máximo de {$maxPendientes} citas pendientes.",
            ]);
        }

        $cita = $this->crearCita(
            $datos,
            (int) $paciente->id_paciente,
            (int) $usuario->id_usuario
        );

        $correo = $paciente->correo ?: $usuario->correo;
        if ($correo) {
            try {
                Mail::to($correo)->send(new ConfirmacionCitaMail($cita));
            } catch (Throwable $error) {
                Log::warning('No se pudo enviar la confirmación de una cita.', [
                    'id_cita' => $cita->id_cita,
                    'error' => $error->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('portal.citas')
            ->with('success', 'Tu cita quedó reservada. Código: ' . $cita->codigo);
    }

    public function show(Cita $cita): View
    {
        $this->autorizarLecturaCita($cita);
        $cita->load(['paciente', 'medico', 'especialidad', 'historial.usuario']);

        return view('citas.show', compact('cita'));
    }

    public function edit(Request $request, Cita $cita): View
    {
        abort_unless($request->user()->role?->nombre === 'Recepcionista', 403);
        abort_unless(in_array($cita->estado, ['Programada', 'Confirmada'], true), 403);

        $cita->load(['paciente', 'medico', 'especialidad']);
        $fecha = $request->query('fecha');
        $opciones = [];

        if ($fecha !== null) {
            $datos = $request->validate([
                'fecha' => $this->reglasFechaReserva(),
            ], $this->mensajesFechaReserva());
            $fecha = $datos['fecha'];
            $opciones = $this->disponibilidad->buscar(
                (int) $cita->id_especialidad,
                (int) $cita->id_medico,
                $fecha,
                (int) $cita->id_cita
            );
        }

        return view('citas.edit', compact('cita', 'fecha', 'opciones'));
    }

    public function update(Request $request, Cita $cita): RedirectResponse
    {
        abort_unless($request->user()->role?->nombre === 'Recepcionista', 403);
        abort_unless(in_array($cita->estado, ['Programada', 'Confirmada'], true), 403);

        $datos = $request->validate([
            'id_especialidad' => [
                'required',
                'integer',
                Rule::exists('especialidades', 'id_especialidad')->where('estado', true),
            ],
            'fecha' => $this->reglasFechaReserva(),
            'hora_inicio' => ['required', 'date_format:H:i'],
        ], $this->mensajesFechaReserva());

        DB::transaction(function () use ($cita, $datos, $request): void {
            $medico = Medico::query()
                ->whereKey($cita->id_medico)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                !$medico->estado
                || !$medico->especialidades()
                    ->where('especialidades.id_especialidad', $datos['id_especialidad'])
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'id_especialidad' => 'El médico no atiende esa especialidad.',
                ]);
            }

            if (!$this->disponibilidad->estaDisponible(
                (int) $datos['id_especialidad'],
                (int) $cita->id_medico,
                $datos['fecha'],
                $datos['hora_inicio'],
                (int) $cita->id_cita
            )) {
                throw ValidationException::withMessages([
                    'hora_inicio' => 'Ese espacio ya no está disponible. Busca otro horario.',
                ]);
            }

            $this->validarChoquePaciente(
                (int) $cita->id_paciente,
                $datos['fecha'],
                $datos['hora_inicio'],
                (int) $cita->id_cita
            );

            $fechaAnterior = $cita->fecha->format('Y-m-d');
            $horaAnterior = substr((string) $cita->hora_inicio, 0, 8);
            $horaFin = Carbon::parse($datos['fecha'] . ' ' . $datos['hora_inicio'])
                ->addMinutes(DisponibilidadCitasService::INTERVALO_MINUTOS)
                ->format('H:i:s');

            $cita->update([
                'id_especialidad' => $datos['id_especialidad'],
                'fecha' => $datos['fecha'],
                'hora_inicio' => $datos['hora_inicio'],
                'hora_fin' => $horaFin,
            ]);

            $this->guardarHistorial(
                $cita,
                'Reprogramacion',
                $cita->estado,
                $cita->estado,
                "Reprogramada al {$datos['fecha']} a las {$datos['hora_inicio']}",
                $request->user()->id_usuario,
                $fechaAnterior,
                $horaAnterior
            );
        });

        return redirect()
            ->route('citas.show', $cita)
            ->with('success', 'La cita se reprogramó correctamente.');
    }

    public function cancelar(Request $request, Cita $cita): RedirectResponse
    {
        $usuario = $request->user();
        $esPaciente = $usuario->role?->nombre === 'Paciente';

        if ($esPaciente) {
            $paciente = Paciente::query()->where('id_usuario', $usuario->id_usuario)->firstOrFail();
            abort_unless((int) $cita->id_paciente === (int) $paciente->id_paciente, 403);

            $horasMinimas = $this->parametroEntero('anticipacion_cancelacion_horas', 2);
            $inicioCita = Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio);
            if (now()->diffInMinutes($inicioCita, false) < $horasMinimas * 60) {
                throw ValidationException::withMessages([
                    'motivo_cancelacion' => 'La cita está dentro del plazo mínimo. Contacta a recepción para cancelarla.',
                ]);
            }
        }

        abort_unless(
            $esPaciente || $usuario->role?->nombre === 'Recepcionista',
            403
        );
        abort_unless(in_array($cita->estado, ['Programada', 'Confirmada'], true), 403);

        $datos = $request->validate([
            'motivo_cancelacion' => ['required', 'string', 'max:200'],
        ]);

        DB::transaction(function () use ($cita, $datos, $usuario): void {
            $estadoAnterior = $cita->estado;
            $cita->update([
                'estado' => 'Cancelada',
                'motivo_cancelacion' => $datos['motivo_cancelacion'],
            ]);

            $this->guardarHistorial(
                $cita,
                'Cancelacion',
                $estadoAnterior,
                'Cancelada',
                $datos['motivo_cancelacion'],
                $usuario->id_usuario
            );
        });

        return back()->with('success', 'La cita fue cancelada y el horario quedó libre.');
    }

    public function cambiarEstado(Request $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(['Confirmada', 'Atendida', 'Inasistencia', 'Programada'])],
            'detalle' => ['nullable', 'string', 'max:250'],
        ]);

        $usuario = $request->user();
        $rol = $usuario->role?->nombre;
        $nuevoEstado = $datos['estado'];

        if ($rol === 'Recepcionista') {
            abort_unless(in_array($nuevoEstado, ['Confirmada', 'Inasistencia'], true), 403);
        } elseif ($rol === 'Medico') {
            $medico = Medico::query()->where('id_usuario', $usuario->id_usuario)->firstOrFail();
            abort_unless(
                $nuevoEstado === 'Atendida'
                && $medico->puede_marcar_atendida
                && (int) $cita->id_medico === (int) $medico->id_medico,
                403
            );
        } elseif ($rol === 'Administrador') {
            abort_unless($cita->estado === 'Cancelada' && $nuevoEstado === 'Programada', 403);
            if (blank($datos['detalle'] ?? null)) {
                throw ValidationException::withMessages([
                    'detalle' => 'Escribe el motivo para reabrir la cita.',
                ]);
            }

            if (!$this->disponibilidad->estaDisponible(
                (int) $cita->id_especialidad,
                (int) $cita->id_medico,
                $cita->fecha->format('Y-m-d'),
                substr((string) $cita->hora_inicio, 0, 5),
                (int) $cita->id_cita
            )) {
                throw ValidationException::withMessages([
                    'estado' => 'El horario ya está ocupado; no se puede reabrir la cita.',
                ]);
            }
        } else {
            abort(403);
        }

        $permitidas = [
            'Programada' => ['Confirmada', 'Atendida', 'Inasistencia', 'Cancelada'],
            'Confirmada' => ['Atendida', 'Inasistencia', 'Cancelada'],
            'Cancelada' => ['Programada'],
            'Atendida' => [],
            'Inasistencia' => [],
        ];

        abort_unless(
            in_array($nuevoEstado, $permitidas[$cita->estado] ?? [], true),
            422,
            'La transición de estado no está permitida.'
        );

        if (in_array($nuevoEstado, ['Atendida', 'Inasistencia'], true)) {
            $inicioCita = Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio);
            abort_if($inicioCita->isFuture(), 422, 'Una cita futura no se puede marcar como atendida o inasistencia.');
        }

        DB::transaction(function () use ($cita, $nuevoEstado, $datos, $usuario): void {
            $anterior = $cita->estado;
            $cita->update([
                'estado' => $nuevoEstado,
                'motivo_cancelacion' => $nuevoEstado === 'Programada' ? null : $cita->motivo_cancelacion,
            ]);

            $this->guardarHistorial(
                $cita,
                $nuevoEstado === 'Programada' ? 'Reapertura' : 'Cambio de estado',
                $anterior,
                $nuevoEstado,
                $datos['detalle'] ?? 'Cambio de estado de la cita',
                $usuario->id_usuario
            );
        });

        return back()->with('success', 'El estado de la cita se actualizó correctamente.');
    }

    public function agenda(Request $request): View
    {
        abort_unless(
            in_array($request->user()->role?->nombre, ['Medico', 'Recepcionista', 'Administrador'], true),
            403
        );

        $datos = $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
            'id_medico' => ['nullable', 'integer', 'exists:medicos,id_medico'],
            'estado' => ['nullable', Rule::in(['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'Inasistencia'])],
        ]);

        $rol = $request->user()->role?->nombre;
        $medicos = Medico::query()->where('estado', true)->orderBy('apellidos')->get();
        $idMedico = $datos['id_medico'] ?? null;

        if ($rol === 'Medico') {
            $idMedico = Medico::query()
                ->where('id_usuario', $request->user()->id_usuario)
                ->value('id_medico');

            abort_if(!$idMedico, 403, 'Tu usuario no está vinculado a un perfil médico.');
        }

        $fecha = $datos['fecha'] ?? today()->toDateString();
        $citas = Cita::query()
            ->with(['paciente', 'medico', 'especialidad'])
            ->whereDate('fecha', $fecha)
            ->when($idMedico, fn ($consulta) => $consulta->where('id_medico', $idMedico))
            ->when($datos['estado'] ?? null, fn ($consulta, $estado) => $consulta->where('estado', $estado))
            ->orderBy('hora_inicio')
            ->get();

        return view('citas.agenda', compact('citas', 'medicos', 'fecha', 'idMedico', 'rol'));
    }

    public function historialPaciente(Request $request, Paciente $paciente): View
    {
        abort_unless(
            in_array($request->user()->role?->nombre, ['Recepcionista', 'Administrador'], true),
            403
        );

        $datos = $request->validate([
            'estado' => ['nullable', Rule::in(['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'Inasistencia'])],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
        ]);

        $citas = Cita::query()
            ->with(['medico', 'especialidad', 'historial.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->when($datos['estado'] ?? null, fn ($consulta, $estado) => $consulta->where('estado', $estado))
            ->when($datos['desde'] ?? null, fn ($consulta, $desde) => $consulta->whereDate('fecha', '>=', $desde))
            ->when($datos['hasta'] ?? null, fn ($consulta, $hasta) => $consulta->whereDate('fecha', '<=', $hasta))
            ->orderByDesc('fecha')
            ->orderByDesc('hora_inicio')
            ->get();

        return view('citas.historial-paciente', compact('paciente', 'citas', 'datos'));
    }

    private function validarFiltros(Request $request): array
    {
        return $request->validate([
            'id_especialidad' => [
                'required',
                'integer',
                Rule::exists('especialidades', 'id_especialidad')->where('estado', true),
            ],
            'id_medico' => [
                'nullable',
                'integer',
                Rule::exists('medicos', 'id_medico')->where('estado', true),
            ],
            'fecha' => $this->reglasFechaReserva(),
            'consultorio' => ['nullable', 'string', 'max:40'],
        ], $this->mensajesFechaReserva());
    }

    private function reglasReserva(): array
    {
        return [
            'id_especialidad' => [
                'required',
                'integer',
                Rule::exists('especialidades', 'id_especialidad')->where('estado', true),
            ],
            'id_medico' => [
                'required',
                'integer',
                Rule::exists('medicos', 'id_medico')->where('estado', true),
            ],
            'fecha' => $this->reglasFechaReserva(),
            'hora_inicio' => ['required', 'date_format:H:i'],
            'modalidad' => ['required', Rule::in(['Presencial', 'Virtual'])],
            'motivo' => ['nullable', 'string', 'max:200'],
        ];
    }

    private function reglasFechaReserva(): array
    {
        return [
            'required',
            'date_format:Y-m-d',
            'after_or_equal:today',
            'before_or_equal:' . today()->addDays(13)->toDateString(),
        ];
    }

    private function mensajesFechaReserva(): array
    {
        return [
            'fecha.before_or_equal' => 'Solo puedes consultar o reservar horarios dentro de las próximas dos semanas.',
        ];
    }

    private function crearCita(array $datos, int $idPaciente, int $idUsuario): Cita
    {
        return DB::transaction(function () use ($datos, $idPaciente, $idUsuario): Cita {
            $medico = Medico::query()
                ->whereKey($datos['id_medico'])
                ->lockForUpdate()
                ->firstOrFail();

            $paciente = Paciente::query()
                ->whereKey($idPaciente)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$medico->estado || !$paciente->estado) {
                throw ValidationException::withMessages([
                    'id_medico' => 'El médico o el paciente están inactivos.',
                ]);
            }

            if (!$medico->especialidades()
                ->where('especialidades.id_especialidad', $datos['id_especialidad'])
                ->exists()) {
                throw ValidationException::withMessages([
                    'id_especialidad' => 'El médico no atiende esa especialidad.',
                ]);
            }

            if (!$this->disponibilidad->estaDisponible(
                (int) $datos['id_especialidad'],
                (int) $datos['id_medico'],
                $datos['fecha'],
                $datos['hora_inicio']
            )) {
                throw ValidationException::withMessages([
                    'hora_inicio' => 'Ese espacio ya no está disponible. Busca otro horario.',
                ]);
            }

            $this->validarChoquePaciente($idPaciente, $datos['fecha'], $datos['hora_inicio']);

            $horaFin = Carbon::parse($datos['fecha'] . ' ' . $datos['hora_inicio'])
                ->addMinutes(DisponibilidadCitasService::INTERVALO_MINUTOS)
                ->format('H:i:s');

            $cita = Cita::create([
                'codigo' => 'TMP-' . Str::upper(Str::random(7)),
                'id_paciente' => $idPaciente,
                'id_medico' => $medico->id_medico,
                'id_especialidad' => $datos['id_especialidad'],
                'fecha' => $datos['fecha'],
                'hora_inicio' => $datos['hora_inicio'],
                'hora_fin' => $horaFin,
                'modalidad' => $datos['modalidad'],
                'motivo' => $datos['motivo'] ?? null,
                'estado' => 'Programada',
                'id_usuario_registra' => $idUsuario,
            ]);

            $cita->update(['codigo' => sprintf('CM-%04d', $cita->id_cita)]);

            $this->guardarHistorial(
                $cita,
                'Registro',
                null,
                'Programada',
                'Cita registrada',
                $idUsuario
            );

            return $cita;
        });
    }

    private function validarChoquePaciente(
        int $idPaciente,
        string $fecha,
        string $horaInicio,
        ?int $excluirCita = null
    ): void {
        $inicio = Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = $inicio->copy()->addMinutes(DisponibilidadCitasService::INTERVALO_MINUTOS);

        $hayChoque = Cita::query()
            ->where('id_paciente', $idPaciente)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'Cancelada')
            ->when(
                $excluirCita,
                fn ($consulta) => $consulta->where('id_cita', '!=', $excluirCita)
            )
            ->where('hora_inicio', '<', $fin->format('H:i:s'))
            ->where('hora_fin', '>', $inicio->format('H:i:s'))
            ->exists();

        if ($hayChoque) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'El paciente ya tiene otra cita en ese horario.',
            ]);
        }
    }

    private function guardarHistorial(
        Cita $cita,
        string $evento,
        ?string $estadoAnterior,
        string $estadoNuevo,
        ?string $detalle,
        ?int $idUsuario,
        ?string $fechaAnterior = null,
        ?string $horaAnterior = null
    ): void {
        HistorialCita::create([
            'id_cita' => $cita->id_cita,
            'evento' => $evento,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'fecha_anterior' => $fechaAnterior,
            'hora_anterior' => $horaAnterior,
            'detalle' => $detalle,
            'id_usuario' => $idUsuario,
            'fecha_evento' => now(),
        ]);
    }

    private function autorizarLecturaCita(Cita $cita): void
    {
        $usuario = request()->user();
        $rol = $usuario->role?->nombre;
        abort_unless(in_array($rol, ['Paciente', 'Medico', 'Recepcionista', 'Administrador'], true), 403);

        if ($rol === 'Paciente') {
            $idPaciente = Paciente::query()
                ->where('id_usuario', $usuario->id_usuario)
                ->value('id_paciente');
            abort_unless((int) $idPaciente === (int) $cita->id_paciente, 403);
        } elseif ($rol === 'Medico') {
            $idMedico = Medico::query()
                ->where('id_usuario', $usuario->id_usuario)
                ->value('id_medico');
            abort_unless((int) $idMedico === (int) $cita->id_medico, 403);
        }
    }

    private function parametroEntero(string $clave, int $defecto): int
    {
        return (int) (ParametroSistema::query()->where('clave', $clave)->value('valor') ?? $defecto);
    }
}
