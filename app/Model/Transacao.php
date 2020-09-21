<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**

 * @property int cedente_id,
 * @property int beneficiario_id,
 * @property float valor,
 * @property int status,
 * @property string mensagem,
 */
class Transacao extends Model
{
    const STATUS_TRANSFERIDO = 1;
    const STATUS_PROCESSANDO = 2;
    const STATUS_RECUSADO = 3;

    protected $table = 'transacoes';

    /**
     * @var array
     */
    protected $fillable = [
        'cedente_id',
        'beneficiario_id',
        'valor',
        'status',
        'mensagem',
    ];

    /**
     * @var array
     */
    protected $hidden = [];

    /**
     * @var array
     */
    protected $casts = [
        'status' => 'integer'
    ];

    /**
     * @return BelongsTo
     */
    public function cedente()
    {
        return $this->belongsTo(Usuario::class, 'cedente_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function beneficiario()
    {
        return $this->belongsTo(Usuario::class, 'beneficiario_id')->withDefault();
    }
}
