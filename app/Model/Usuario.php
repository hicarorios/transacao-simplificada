<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    const TIPO_USUARIO = 1;
    const TIPO_LOJISTA = 2;

    /**
     * @var array
     */
    protected $fillable = [
        'nome',
        'cpf-cnpj',
        'email',
        'senha',
        'tipo',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'senha',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'tipo' => 'integer',
    ];
}
