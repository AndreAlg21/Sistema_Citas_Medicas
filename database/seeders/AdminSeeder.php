<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $nombre = env('INITIAL_ADMIN_NAME');
        $usuario = env('INITIAL_ADMIN_USERNAME');
        $correo = env('INITIAL_ADMIN_EMAIL');
        $contrasena = env('INITIAL_ADMIN_PASSWORD');

        if (!$nombre || !$usuario || !$correo || !$contrasena) {
            throw new RuntimeException(
                'Completa INITIAL_ADMIN_NAME, INITIAL_ADMIN_USERNAME, INITIAL_ADMIN_EMAIL y INITIAL_ADMIN_PASSWORD en .env.'
            );
        }

        $rol = Role::query()
            ->where('nombre', 'Administrador')
            ->firstOrFail();

        User::firstOrCreate(
            ['nombre_usuario' => $usuario],
            [
                'id_rol' => $rol->id_rol,
                'nombre_completo' => $nombre,
                'correo' => $correo,
                'contrasena' => $contrasena,
                'estado' => true,
                'intentos_fallidos' => 0,
            ]
        );
    }
}