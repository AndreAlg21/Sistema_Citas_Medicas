<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueoAgenda extends Model
{
    protected $table = 'bloqueos_agenda';
    protected $primaryKey = 'id_bloqueo';
    public $timestamps = false;

    protected $fillable = [
        'id_medico',
        'fecha',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}