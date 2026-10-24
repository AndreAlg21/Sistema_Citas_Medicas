<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cita extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'codigo',
        'id_paciente',
        'id_medico',
        'id_especialidad',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'modalidad',
        'motivo',
        'estado',
        'motivo_cancelacion',
        'id_usuario_registra',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(
            Paciente::class,
            'id_paciente',
            'id_paciente'
        );
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(
            Especialidad::class,
            'id_especialidad',
            'id_especialidad'
        );
    }

    public function usuarioRegistra(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_usuario_registra',
            'id_usuario'
        );
    }

    public function historial(): HasMany
    {
        return $this->hasMany(
            HistorialCita::class,
            'id_cita',
            'id_cita'
        );
    }

    public function recordatorios(): HasMany
    {
        return $this->hasMany(
            RecordatorioCita::class,
            'id_cita',
            'id_cita'
        );
    }
}