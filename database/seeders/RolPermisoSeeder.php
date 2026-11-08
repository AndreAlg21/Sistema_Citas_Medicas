<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisosPorRol = [
            'Recepcionista' => [
                'auth.iniciar_sesion',
                'auth.cerrar_sesion',
                'auth.recuperar_contrasena',
                'pacientes.gestionar',
                'citas.consultar_disponibilidad',
                'citas.registrar',
                'citas.reprogramar',
                'citas.cancelar',
                'citas.controlar_asistencia',
                'agenda.consultar',
                'citas.consultar_historial',
                'reportes.generar',
            ],
            'Medico' => [
                'auth.iniciar_sesion',
                'auth.cerrar_sesion',
                'auth.recuperar_contrasena',
                'citas.controlar_asistencia',
                'agenda.consultar',
            ],
            'Paciente' => [
                'auth.iniciar_sesion',
                'auth.cerrar_sesion',
                'auth.recuperar_contrasena',
                'citas.consultar_disponibilidad',
                'citas.cancelar',
                'pacientes.registrar_cuenta',
                'pacientes.gestionar_perfil',
                'citas.reservar_en_linea',
                'citas.consultar_mis_citas',
                'historial_medico.consultar',
            ],
            'Administrador' => [
                'auth.iniciar_sesion',
                'auth.cerrar_sesion',
                'auth.recuperar_contrasena',
                'usuarios.gestionar',
                'roles_permisos.gestionar',
                'bitacoras.consultar',
                'especialidades.gestionar',
                'medicos.gestionar',
                'horarios.gestionar',
                'pacientes.gestionar',
                'agenda.consultar',
                'citas.consultar_historial',
                'citas.controlar_asistencia',
                'reportes.generar',
            ],
        ];

        foreach ($permisosPorRol as $nombreRol => $codigos) {
            $rol = Role::query()
                ->where('nombre', $nombreRol)
                ->firstOrFail();

            $idsPermisos = Permiso::query()
                ->whereIn('codigo', $codigos)
                ->pluck('id_permiso')
                ->all();

            // Reemplaza los permisos anteriores para retirar accesos sobrantes
            // y dejar exactamente los asignados a cada actor en el documento.
            $rol->permisos()->sync($idsPermisos);
        }
    }
}
