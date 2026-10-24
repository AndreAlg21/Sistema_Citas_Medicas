<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    protected $table = 'medicos';
    protected $primaryKey = 'id_medico';

    protected $fillable = [
        'codigo',
        'id_usuario',
        'registro_profesional',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'puede_marcar_atendida',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'puede_marcar_atendida' => 'boolean',
            'estado' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function especialidades(): BelongsToMany
    {
        return $this->belongsToMany(
            Especialidad::class,
            'medico_especialidad',
            'id_medico',
            'id_especialidad'
        );
    }

    public function horariosAtencion(): HasMany
    {
        return $this->hasMany(Horario::class, 'id_medico', 'id_medico');
    }

    public function bloqueosAgenda(): HasMany
    {
        return $this->hasMany(
            BloqueoAgenda::class,
            'id_medico',
            'id_medico'
        );
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_medico', 'id_medico');
    }
}