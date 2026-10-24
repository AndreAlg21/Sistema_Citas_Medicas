<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialCita extends Model
{
    protected $table = 'historial_citas';
    protected $primaryKey = 'id_historial';
    public $timestamps = false;

    protected $fillable = [
        'id_cita',
        'evento',
        'estado_anterior',
        'estado_nuevo',
        'fecha_anterior',
        'hora_anterior',
        'detalle',
        'id_usuario',
        'fecha_evento',
    ];

    protected function casts(): array
    {
        return [
            'fecha_anterior' => 'date',
            'fecha_evento' => 'datetime',
        ];
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}