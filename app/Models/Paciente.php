<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paciente extends Model
{
    protected $table = 'pacientes';
    protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'codigo',
        'id_usuario',
        'ci',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'telefono',
        'correo',
        'direccion',
        'acepta_notificaciones',
        'acepta_privacidad',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'acepta_notificaciones' => 'boolean',
            'acepta_privacidad' => 'boolean',
            'estado' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function informacionMedica(): HasOne
    {
        return $this->hasOne(Perfilpaciente::class, 'id_paciente', 'id_paciente');
    }

    public function contactosEmergencia(): HasMany
    {
        return $this->hasMany(
            ContactoEmergencia::class,
            'id_paciente',
            'id_paciente'
        );
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }
}