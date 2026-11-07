<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsuarioDemostracionSeeder extends Seeder
{
    public function run(): void
    {
        // Estas credenciales son solo para desarrollo y nunca se crean en producción.
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        $cuentas = [
            'Administrador' => [
                'nombre' => 'Admin Demo',
                'usuario' => 'admin',
                'correo' => 'admin.demo@clinica.test',
            ],
            'Recepcionista' => [
                'nombre' => 'Recepción Demo',
                'usuario' => 'recepcion',
                'correo' => 'recepcion.demo@clinica.test',
            ],
            'Medico' => [
                'nombre' => 'Médico Demo',
                'usuario' => 'medico',
                'correo' => 'medico.demo@clinica.test',
            ],
            'Paciente' => [
                'nombre' => 'Paciente Demo',
                'usuario' => 'paciente',
                'correo' => 'paciente.demo@clinica.test',
            ],
        ];

        foreach ($cuentas as $nombreRol => $datos) {
            $rol = Role::query()->where('nombre', $nombreRol)->firstOrFail();

            $usuario = User::query()->updateOrCreate(
                ['correo' => $datos['correo']],
                [
                    'id_rol' => $rol->id_rol,
                    'nombre_completo' => $datos['nombre'],
                    'nombre_usuario' => $datos['usuario'],
                    'contrasena' => '123456',
                    'estado' => true,
                ]
            );

            if ($nombreRol === 'Medico') {
                $medico = Medico::query()->updateOrCreate(
                    ['id_usuario' => $usuario->id_usuario],
                    [
                        'codigo' => 'MED-DEMO',
                        'registro_profesional' => 'REG-DEMO-001',
                        'nombres' => 'Médico',
                        'apellidos' => 'Demo',
                        'telefono' => '70000001',
                        'correo' => $datos['correo'],
                        'puede_marcar_atendida' => true,
                        'estado' => true,
                    ]
                );

                $especialidad = Especialidad::query()
                    ->where('nombre_normalizado', 'MEDICINA GENERAL')
                    ->first();

                if ($especialidad) {
                    $medico->especialidades()->syncWithoutDetaching([$especialidad->id_especialidad]);
                }
            }

            if ($nombreRol === 'Paciente') {
                Paciente::query()->updateOrCreate(
                    ['id_usuario' => $usuario->id_usuario],
                    [
                        'codigo' => 'P-DEMO',
                        'ci' => 'CI-DEMO-001',
                        'nombres' => 'Paciente',
                        'apellidos' => 'Demo',
                        'fecha_nacimiento' => '1995-01-01',
                        'telefono' => '70000002',
                        'correo' => $datos['correo'],
                        'acepta_notificaciones' => true,
                        'acepta_privacidad' => true,
                        'estado' => true,
                    ]
                );
            }
        }
    }
}
