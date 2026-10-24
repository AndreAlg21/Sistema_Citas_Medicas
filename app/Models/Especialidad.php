<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    protected $table = 'especialidades';
    protected $primaryKey = 'id_especialidad';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'nombre_normalizado',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function medicos(): BelongsToMany
    {
        return $this->belongsToMany(
            Medico::class,
            'medico_especialidad',
            'id_especialidad',
            'id_medico'
        );
    }

    public function citas(): HasMany
    {
        return $this->hasMany(
            Cita::class,
            'id_especialidad',
            'id_especialidad'
        );
    }
}