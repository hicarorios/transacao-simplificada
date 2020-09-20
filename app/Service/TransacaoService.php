<?php

namespace App\Service;

use App\Model\Usuario;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class TransacaoService
{
    const MENSAGEM_AUTORIZACAO_TRANSACAO = 'Autorizado';
    const URL_AUTORIZACAO_TRANSACAO = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

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

        DB::transaction(function () use ($dadosTransferencia, $usuarioPagador, $usuarioBeneficiario) {
            $usuarioPagador->carteira->saldo -= $dadosTransferencia->value;
            $usuarioBeneficiario->carteira->saldo += $dadosTransferencia->value;

            $usuarioPagador->carteira->save();
            $usuarioBeneficiario->carteira->save();

            if ($usuarioPagador->carteira->saldo < 0) {
                throw new DomainException("Insufficient funds to make a transaction");
            }

            $autorizacaoTransacao = Http::get(self::URL_AUTORIZACAO_TRANSACAO);

            if (isset($autorizacaoTransacao) && $autorizacaoTransacao['message'] != self::MENSAGEM_AUTORIZACAO_TRANSACAO) {
                throw new DomainException("Transaction not authorized");
            }
        });
    }
}