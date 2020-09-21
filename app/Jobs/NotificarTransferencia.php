<?php

namespace App\Jobs;

use App\Model\Transacao;
use App\Service\TransacaoEnum;
use DomainException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class NotificarTransferencia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Transacao $transacao;

    public function __construct(Transacao $transacao)
    {
        $this->transacao = $transacao;
    }

    public function handle()
    {
        $notificacaoTransacao = Http::post(TransacaoEnum::URL_NOTIFICACAO_TRANSACAO);

        $notificacaoTransacao->throw();

        if ($notificacaoTransacao['message'] != TransacaoEnum::MENSAGEM_NOTIFICACAO_TRANSACAO) {
            throw new DomainException('Notification could not be sent');
        }
    }
}
