<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $table = 'horarios_atencion';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'id_medico',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'duracion_cita_min',
        'consultorio',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'dia_semana' => 'integer',
            'duracion_cita_min' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}
