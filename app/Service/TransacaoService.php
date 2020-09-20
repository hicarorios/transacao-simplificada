<?php

namespace App\Service;

use App\Model\Usuario;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransacaoService
{
    private Usuario $usuarioModel;

    public function __construct(Usuario $usuario)
    {
        $this->usuarioModel = $usuario;
    }

    public function efetuarTransacao(Request $dadosTransferencia)
    {
        $usuarioPagador = $this->usuarioModel
            ->with('carteira')
            ->find($dadosTransferencia->payer);

        $usuarioBeneficiario = $this->usuarioModel
            ->with('carteira')
            ->find($dadosTransferencia->payee);

        if ($usuarioPagador->tipo == \App\Model\Usuario::TIPO_LOJISTA) {
            throw new DomainException("Users of type Lojista, can't make transactions");
        }

        $usuarioPagador->carteira->saldo -= $dadosTransferencia->value;
        $usuarioBeneficiario->carteira->saldo += $dadosTransferencia->value;

        if ($usuarioPagador->carteira->saldo < 0) {
            throw new DomainException("Insufficient funds to make a transaction");
        }

        DB::transaction(function () use ($usuarioPagador, $usuarioBeneficiario) {
            $usuarioPagador->carteira->save();
            $usuarioBeneficiario->carteira->save();
        });
    }
}