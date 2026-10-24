<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroSistema extends Model
{
    protected $table = 'parametros_sistema';
    protected $primaryKey = 'id_parametro';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];
}