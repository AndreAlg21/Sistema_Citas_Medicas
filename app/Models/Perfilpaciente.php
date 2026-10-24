<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfilpaciente extends Model
{
    protected $table = 'informacion_medica';
    protected $primaryKey = 'id_info';

    public const CREATED_AT = null;
    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id_paciente',
        'tipo_sangre',
        'alergias',
        'enfermedades_cronicas',
        'medicamentos',
        'antecedentes',
    ];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
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
}