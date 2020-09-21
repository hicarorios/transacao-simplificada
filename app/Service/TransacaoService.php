<?php

namespace App\Service;

use App\Jobs\NotificarTransferencia;
use App\Model\Transacao;
use App\Model\Usuario;
use DomainException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransacaoService
{
    private Usuario $usuarioModel;
    private Transacao $transacaoModel;

    public function __construct(Usuario $usuario, Transacao $transacao)
    {
        $this->usuarioModel = $usuario;
        $this->transacaoModel = $transacao;
    }

    /**
     * @param Request $dadosTransferencia
     * @throws MassAssignmentException
     * @return Transacao
     */
    public function iniciarTransacao(Request $dadosTransferencia): Transacao
    {
        return $this->transacaoModel->create([
            'cedente_id' => $dadosTransferencia->payer,
            'beneficiario_id' => $dadosTransferencia->payee,
            'valor' =>  $dadosTransferencia->value,
            'status' => Transacao::STATUS_PROCESSANDO,
            'mensagem' => 'Transaction processing',
        ]);
    }

    /**
     * @param Request $dadosTransferencia
     * @param Transacao $transacao
     * @return Transacao
     * @throws MassAssignmentException
     * @throws DomainException
     */
    public function efetuarTransacao(Transacao $transacao, Request $dadosTransferencia): Transacao
    {
        $usuarioCedente = $this->usuarioModel
            ->with('carteira')
            ->find($dadosTransferencia->payer);

        $usuarioBeneficiario = $this->usuarioModel
            ->with('carteira')
            ->find($dadosTransferencia->payee);

        if ($usuarioCedente->tipo == \App\Model\Usuario::TIPO_LOJISTA) {
            $mensagem = "Users of type Lojista, can't make transactions";
            $transacao->update(['status' => Transacao::STATUS_RECUSADO, 'mensagem' => $mensagem]);
            throw new DomainException($mensagem);
        }

        if ($usuarioCedente->carteira->saldo == 0) {
            $mensagem = "Insufficient funds to make a transaction";
            $transacao->update(['status' => Transacao::STATUS_RECUSADO, 'mensagem' => $mensagem]);
            throw new DomainException($mensagem);
        }

        $this->transferirValor($transacao, $usuarioCedente, $usuarioBeneficiario, $dadosTransferencia->value);

        $transacao->update([
            'status' => Transacao::STATUS_TRANSFERIDO,
            'mensagem' => 'The amount has been transferred',
        ]);

        NotificarTransferencia::dispatch($transacao);

        return $transacao;
    }

    /**
     * @param Transacao $transacao
     * @param Usuario $usuarioCedente
     * @param Usuario $usuarioBeneficiario
     * @param float $valor
     * @return $transacao
     * @throws DomainException
     */
    private function transferirValor(Transacao $transacao, Usuario $usuarioCedente, Usuario $usuarioBeneficiario, $valor): Transacao
    {
        DB::beginTransaction();

        $usuarioCedente->carteira->saldo -= $valor;
        $usuarioBeneficiario->carteira->saldo += $valor;

        $usuarioCedente->carteira->save();
        $usuarioBeneficiario->carteira->save();

        if ($usuarioCedente->carteira->saldo < 0) {
            DB::rollBack();

            $mensagem = "Insufficient funds to make a transaction";
            $transacao->update(['status' => Transacao::STATUS_RECUSADO, 'mensagem' => $mensagem]);
            throw new DomainException($mensagem);
        }

        $autorizacaoTransacao = Http::get(TransacaoEnum::URL_AUTORIZACAO_TRANSACAO);

        if (isset($autorizacaoTransacao) && $autorizacaoTransacao['message'] != TransacaoEnum::MENSAGEM_AUTORIZACAO_TRANSACAO) {
            DB::rollBack();

            $mensagem = "Transaction not authorized";
            $transacao->update(['status' => Transacao::STATUS_RECUSADO, 'mensagem' => $mensagem]);
            throw new DomainException($mensagem);
        }

        DB::commit();

        return $transacao;
    }
}
