<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            [
                'nombre' => 'Ginecología',
                'nombre_normalizado' => 'GINECOLOGIA',
                'descripcion' => 'Atención de la salud del sistema reproductivo femenino.',
            ],
            [
                'nombre' => 'Pediatría',
                'nombre_normalizado' => 'PEDIATRIA',
                'descripcion' => 'Atención médica de niñas, niños y adolescentes.',
            ],
            [
                'nombre' => 'Medicina General',
                'nombre_normalizado' => 'MEDICINA GENERAL',
                'descripcion' => 'Atención primaria y evaluación general de la salud.',
            ],
            [
                'nombre' => 'Geriatría',
                'nombre_normalizado' => 'GERIATRIA',
                'descripcion' => 'Atención integral de la salud de las personas adultas mayores.',
            ],
            [
                'nombre' => 'Odontología',
                'nombre_normalizado' => 'ODONTOLOGIA',
                'descripcion' => 'Prevención y atención de la salud bucodental.',
            ],
            [
                'nombre' => 'Cardiología',
                'nombre_normalizado' => 'CARDIOLOGIA',
                'descripcion' => 'Evaluación y atención de enfermedades del corazón y del sistema circulatorio.',
            ],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::firstOrCreate(
                ['nombre_normalizado' => $especialidad['nombre_normalizado']],
                [
                    'nombre' => $especialidad['nombre'],
                    'descripcion' => $especialidad['descripcion'],
                    'estado' => true,
                ]
            );
        }
    }
}