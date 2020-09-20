<?php

namespace App\Service;

use App\Model\Usuario;
use DomainException;
use Illuminate\Http\Request;
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

        if ($usuarioPagador->carteira->saldo == 0) {
            throw new DomainException("Insufficient funds to make a transaction");
        }

        

    }
}