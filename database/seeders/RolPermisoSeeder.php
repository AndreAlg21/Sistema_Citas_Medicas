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
                'citas.consultar_disponibilidad',
                'citas.controlar_asistencia',
                'agenda.consultar',
                'citas.consultar_historial',
                'historial_medico.consultar',
            ],
            'Paciente' => [
                'auth.iniciar_sesion',
                'auth.cerrar_sesion',
                'auth.recuperar_contrasena',
                'citas.consultar_disponibilidad',
                'pacientes.registrar_cuenta',
                'pacientes.gestionar_perfil',
                'citas.reservar_en_linea',
                'citas.consultar_mis_citas',
                'historial_medico.consultar',
            ],
        ];

        $administrador = Role::query()
            ->where('nombre', 'Administrador')
            ->firstOrFail();

        $administrador->permisos()->syncWithoutDetaching(
            Permiso::query()->pluck('id_permiso')->all()
        );

        foreach ($permisosPorRol as $nombreRol => $codigos) {
            $rol = Role::query()
                ->where('nombre', $nombreRol)
                ->firstOrFail();

            $idsPermisos = Permiso::query()
                ->whereIn('codigo', $codigos)
                ->pluck('id_permiso')
                ->all();

            $rol->permisos()->syncWithoutDetaching($idsPermisos);
        }
    }
}