<?php

namespace Tests\Unit;

use App\Service\TransacaoEnum;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificarTransferencia extends TestCase
{
    /** @test */
    public function notificacao_de_tranferencia_nao_foi_enviada_api_negou()
    {
        $this->expectException('\DomainException');

        Http::fake([
            TransacaoEnum::URL_NOTIFICACAO_TRANSACAO => Http::response(['message' => 'Erro'])
        ]);

        $transacao = factory(\App\Model\Transacao::class)->create();

        $job = new \App\Jobs\NotificarTransferencia($transacao);
        $job->handle();
    }

    /** @test */
    public function notificacao_de_tranferencia_nao_foi_enviada_api_indisponivel()
    {
        $this->expectException('\Illuminate\Http\Client\RequestException');

        Http::fake([
            TransacaoEnum::URL_NOTIFICACAO_TRANSACAO => Http::response(['message' => TransacaoEnum::MENSAGEM_NOTIFICACAO_TRANSACAO], 404)
        ]);

        $transacao = factory(\App\Model\Transacao::class)->create();

        $job = new \App\Jobs\NotificarTransferencia($transacao);
        $job->handle();
    }
}
