<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_rol',
        'nombre_completo',
        'nombre_usuario',
        'correo',
        'contrasena',
        'telefono',
        'estado',
        'intentos_fallidos',
        'bloqueado_hasta',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
            'estado' => 'boolean',
            'intentos_fallidos' => 'integer',
            'bloqueado_hasta' => 'datetime',
            'ultimo_acceso' => 'datetime',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }
}