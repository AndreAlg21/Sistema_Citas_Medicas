<?php

namespace Database\Seeders;

use App\Models\ParametroSistema;
use Illuminate\Database\Seeder;

class ParametroSistemaSeeder extends Seeder
{
    public function run(): void
    {
        $parametros = [
            ['anticipacion_cancelacion_horas', '2', 'Anticipación mínima para cancelación por el portal.'],
            ['max_citas_pendientes_paciente', '3', 'Máximo de citas futuras pendientes por paciente.'],
            ['horas_recordatorio', '24', 'Antelación para enviar un recordatorio.'],
            ['intervalo_cita_min', '15', 'Separación fija entre los inicios de citas.'],
        ];

        foreach ($parametros as [$clave, $valor, $descripcion]) {
            ParametroSistema::firstOrCreate(
                ['clave' => $clave],
                ['valor' => $valor, 'descripcion' => $descripcion]
            );
        }
    }
}
