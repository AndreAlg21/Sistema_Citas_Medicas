<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['auth.iniciar_sesion', 'Permite iniciar sesión en el sistema.'],
            ['auth.cerrar_sesion', 'Permite cerrar la sesión activa.'],
            ['auth.recuperar_contrasena', 'Permite solicitar la recuperación de contraseña.'],
            ['usuarios.gestionar', 'Permite crear, consultar, actualizar y desactivar usuarios.'],
            ['roles_permisos.gestionar', 'Permite gestionar roles y sus permisos.'],
            ['especialidades.gestionar', 'Permite administrar las especialidades médicas.'],
            ['medicos.gestionar', 'Permite administrar la información de los médicos.'],
            ['horarios.gestionar', 'Permite administrar los horarios de atención.'],
            ['pacientes.gestionar', 'Permite registrar y actualizar pacientes.'],
            ['citas.consultar_disponibilidad', 'Permite consultar la disponibilidad de citas.'],
            ['citas.registrar', 'Permite registrar una cita médica.'],
            ['citas.reprogramar', 'Permite reprogramar una cita médica.'],
            ['citas.cancelar', 'Permite cancelar una cita médica.'],
            ['citas.controlar_asistencia', 'Permite actualizar el estado y la asistencia de una cita.'],
            ['agenda.consultar', 'Permite consultar la agenda médica.'],
            ['citas.consultar_historial', 'Permite consultar el historial de citas de un paciente.'],
            ['reportes.generar', 'Permite generar reportes administrativos de atenciones.'],
            ['pacientes.registrar_cuenta', 'Permite registrar una cuenta de paciente.'],
            ['pacientes.gestionar_perfil', 'Permite consultar y actualizar el perfil del paciente.'],
            ['citas.reservar_en_linea', 'Permite reservar una cita en línea.'],
            ['citas.consultar_mis_citas', 'Permite al paciente consultar sus propias citas.'],
            ['recordatorios.enviar', 'Permite gestionar el envío de recordatorios de citas.'],
            ['historial_medico.consultar', 'Permite consultar la información médica del paciente.'],
            ['bitacoras.consultar', 'Permite consultar los registros de actividad del sistema.'],
            ['parametros.gestionar', 'Permite administrar los parámetros del sistema.'],
        ];

        foreach ($permisos as [$codigo, $descripcion]) {
            Permiso::firstOrCreate(
                ['codigo' => $codigo],
                ['descripcion' => $descripcion]
            );
        }
    }
}