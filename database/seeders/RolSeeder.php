<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['nombre' => 'Administrador'],
            [
                'descripcion' => 'Administra el sistema y sus usuarios.',
                'estado' => true,
            ]
        );

        Role::firstOrCreate(
            ['nombre' => 'Recepcionista'],
            [
                'descripcion' => 'Gestiona pacientes y citas médicas.',
                'estado' => true,
            ]
        );

        Role::firstOrCreate(
            ['nombre' => 'Medico'],
            [
                'descripcion' => 'Consulta su agenda y atiende citas.',
                'estado' => true,
            ]
        );

        Role::firstOrCreate(
            ['nombre' => 'Paciente'],
            [
                'descripcion' => 'Gestiona su perfil y consulta sus citas.',
                'estado' => true,
            ]
        );
    }
}