<?php

namespace App\Services;

use App\Models\BloqueoAgenda;
use App\Models\Cita;
use App\Models\Horario;
use Illuminate\Support\Carbon;

class DisponibilidadCitasService
{
    public const INTERVALO_MINUTOS = 15;

    public function buscar(
        int $idEspecialidad,
        ?int $idMedico,
        string $fecha,
        ?int $excluirCita = null,
        ?string $consultorio = null
    ): array {
        $dia = Carbon::parse($fecha)->dayOfWeekIso;

        // La clínica no atiende fines de semana.
        if ($dia > 5) {
            return [];
        }

        $horarios = Horario::query()
            ->with('medico')
            ->where('estado', true)
            ->where('dia_semana', $dia)
            ->where(function ($consulta) use ($idEspecialidad): void {
                $consulta->where('id_especialidad', $idEspecialidad)
                    ->orWhereNull('id_especialidad');
            })
            ->when($consultorio, fn ($consulta) => $consulta->where('consultorio', $consultorio))
            ->whereHas('medico', function ($consulta) use ($idEspecialidad, $idMedico): void {
                $consulta->where('estado', true)
                    ->whereHas('especialidades', function ($especialidades) use ($idEspecialidad): void {
                        $especialidades->where(
                            'especialidades.id_especialidad',
                            $idEspecialidad
                        );
                    })
                    ->when(
                        $idMedico,
                        fn ($medicos) => $medicos->where('id_medico', $idMedico)
                    );
            })
            ->orderBy('hora_inicio')
            ->get();

        // Si un médico tiene horarios propios para esta especialidad, estos
        // reemplazan su horario genérico. Así cada especialidad puede variar.
        $medicosConHorarioPropio = $horarios
            ->where('id_especialidad', $idEspecialidad)
            ->pluck('id_medico')
            ->unique();
        $horarios = $horarios->filter(fn (Horario $horario): bool =>
            ($horario->id_especialidad !== null && (int) $horario->id_especialidad === $idEspecialidad)
            || ($horario->id_especialidad === null
                && !$medicosConHorarioPropio->contains((int) $horario->id_medico))
        );

        $bloqueos = BloqueoAgenda::query()
            ->whereDate('fecha', $fecha)
            ->get();

        $citas = Cita::query()
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'Cancelada')
            ->when(
                $excluirCita,
                fn ($consulta) => $consulta->where('id_cita', '!=', $excluirCita)
            )
            ->get(['id_medico', 'id_paciente', 'hora_inicio', 'hora_fin']);

        $opciones = [];
        $fechaEsHoy = Carbon::parse($fecha)->isToday();

        foreach ($horarios as $horario) {
            $bloqueado = $bloqueos->contains(
                fn (BloqueoAgenda $bloqueo): bool =>
                    $bloqueo->id_medico === null
                    || (int) $bloqueo->id_medico === (int) $horario->id_medico
            );

            if ($bloqueado) {
                continue;
            }

            $inicioHorario = Carbon::parse($fecha . ' ' . $horario->hora_inicio);
            $finHorario = Carbon::parse($fecha . ' ' . $horario->hora_fin);

            // La clínica abre 07:00–12:00 y 14:00–18:00. Los horarios
            // particulares de cada especialidad se recortan a estas ventanas.
            foreach ([['07:00', '12:00'], ['14:00', '18:00']] as [$apertura, $cierre]) {
                $inicio = $inicioHorario->copy()->max(Carbon::parse($fecha . ' ' . $apertura));
                $finVentana = $finHorario->copy()->min(Carbon::parse($fecha . ' ' . $cierre));

                while ($inicio->copy()->addMinutes(self::INTERVALO_MINUTOS)->lessThanOrEqualTo($finVentana)) {
                    $fin = $inicio->copy()->addMinutes(self::INTERVALO_MINUTOS);

                    if ($fechaEsHoy && $inicio->lessThanOrEqualTo(now())) {
                        $inicio->addMinutes(self::INTERVALO_MINUTOS);
                        continue;
                    }

                    $ocupada = $citas->contains(function (Cita $cita) use ($fecha, $horario, $inicio, $fin): bool {
                        if ((int) $cita->id_medico !== (int) $horario->id_medico) {
                            return false;
                        }

                        $citaInicio = Carbon::parse($fecha . ' ' . $cita->hora_inicio);
                        $citaFin = Carbon::parse($fecha . ' ' . $cita->hora_fin);

                        return $citaInicio->lessThan($fin) && $citaFin->greaterThan($inicio);
                    });

                    if (!$ocupada) {
                        $opciones[] = [
                            'id_medico' => (int) $horario->id_medico,
                            'medico' => $horario->medico->nombres . ' ' . $horario->medico->apellidos,
                            'id_especialidad' => $idEspecialidad,
                            'fecha' => $fecha,
                            'hora_inicio' => $inicio->format('H:i'),
                            'hora_fin' => $fin->format('H:i'),
                            'consultorio' => $horario->consultorio,
                        ];
                    }

                    $inicio->addMinutes(self::INTERVALO_MINUTOS);
                }
            }
        }

        return $opciones;
    }

    public function estaDisponible(
        int $idEspecialidad,
        int $idMedico,
        string $fecha,
        string $horaInicio,
        ?int $excluirCita = null
    ): bool {
        foreach ($this->buscar($idEspecialidad, $idMedico, $fecha, $excluirCita) as $opcion) {
            if ($opcion['hora_inicio'] === substr($horaInicio, 0, 5)) {
                return true;
            }
        }

        return false;
    }
}
