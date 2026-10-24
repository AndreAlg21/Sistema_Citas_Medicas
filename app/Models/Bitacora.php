<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    protected $table = 'bitacoras';
    protected $primaryKey = 'id_bitacora';

    protected $fillable = [
        'tabla_afectada',
        'id_registro_afectado',
        'accion',
        'valores_anteriores',
        'valores_nuevos',
        'fecha_hora',
        'id_usuario',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}