<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Carteira extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'usuario_id', 'saldo'
    ];

    /**
     * @var array
     */
    protected $hidden = [];

    /**
     * @var array
     */
    protected $casts = [];
}
