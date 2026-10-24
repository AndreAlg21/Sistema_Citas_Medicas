<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordatorioCita extends Model
{
    protected $table = 'recordatorios_cita';
    protected $primaryKey = 'id_recordatorio';
    public $timestamps = false;

    protected $fillable = [
        'id_cita',
        'fecha_programada',
        'fecha_envio',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_programada' => 'datetime',
            'fecha_envio' => 'datetime',
        ];
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }
}