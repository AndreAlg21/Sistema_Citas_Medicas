<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenRecuperacion extends Model
{
    protected $table = 'tokens_recuperacion';
    protected $primaryKey = 'id_token';
    public $timestamps = false;

    protected $fillable = [
        'correo',
        'token_hash',
        'expira_en',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'expira_en' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}