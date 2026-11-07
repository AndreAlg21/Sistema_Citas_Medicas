<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioCitaMail;
use App\Models\Cita;
use App\Models\ParametroSistema;
use App\Models\RecordatorioCita;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnviarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:enviar-recordatorios';
    protected $description = 'Envía recordatorios por correo para las próximas citas activas.';

    public function handle(): int
    {
        $horas = (int) (ParametroSistema::query()
            ->where('clave', 'horas_recordatorio')
            ->value('valor') ?? 24);

        $limite = now()->addHours(max(1, $horas));
        $citas = Cita::query()
            ->with(['paciente.usuario', 'medico', 'especialidad'])
            ->whereIn('estado', ['Programada', 'Confirmada'])
            ->whereBetween('fecha', [today()->toDateString(), $limite->toDateString()])
            ->whereDoesntHave(
                'recordatorios',
                fn ($consulta) => $consulta->where('estado', 'enviado')
            )
            ->get();

        foreach ($citas as $cita) {
            $paciente = $cita->paciente;
            $correo = $paciente->correo ?: $paciente->usuario?->correo;
            $inicio = Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio);

            if (
                !$correo
                || !$paciente->acepta_notificaciones
                || $inicio->isPast()
                || $inicio->greaterThan($limite)
            ) {
                continue;
            }

            $registro = RecordatorioCita::create([
                'id_cita' => $cita->id_cita,
                'fecha_programada' => $inicio->copy()->subHours(max(1, $horas)),
                'estado' => 'pendiente',
            ]);

            try {
                Mail::to($correo)->send(new RecordatorioCitaMail($cita));
                $registro->update([
                    'fecha_envio' => now(),
                    'estado' => 'enviado',
                ]);
            } catch (Throwable $error) {
                $registro->update(['estado' => 'error']);
                Log::warning('Falló el envío del recordatorio de una cita.', [
                    'id_cita' => $cita->id_cita,
                    'error' => $error->getMessage(),
                ]);
            }
        }

        $this->info('Se procesaron los recordatorios disponibles.');

        return self::SUCCESS;
    }
}
