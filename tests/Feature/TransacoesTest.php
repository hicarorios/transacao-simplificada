<?php

namespace Tests\Feature;

use App\Jobs\NotificarTransferencia;
use App\Service\TransacaoEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TransacoesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function payload_invalido_nao_e_aceito()
    {
        $this->json('POST', 'api/transaction', [
            'value' => 'asdasdas',
            'payer' => -10,
            'payee' => -5,
        ])->assertStatus(422);
    }

    /** @test */
    public function usuario_transfere_dinheiro_para_usuario()
    {
        $usuarioCedente = factory(\App\Model\Usuario::class)->create();

        factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioCedente->id, 'saldo' => 100]);

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0]);

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioCedente->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('carteiras', [
            'usuario_id' => $usuarioCedente->id,
            'saldo' => 0,
        ]);

        $this->assertDatabaseHas('carteiras', [
            'usuario_id' => $usuarioBeneficiario->id,
            'saldo' => 100,
        ]);
    }

    /** @test */
    public function usuario_lojista_nao_pode_efetuar_tranferencia()
    {
        $usuarioCedente = factory(\App\Model\Usuario::class)
            ->create(['tipo' => \App\Model\Usuario::TIPO_LOJISTA]);

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioCedente->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);
    }

    /** @test */
    public function usuario_sem_saldo_nao_pode_efetuar_transferencia()
    {
        $usuarioCedente = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioCedente = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioCedente->id, 'saldo' => 0])
            ->toArray();

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioBeneficiario = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0])
            ->toArray();

        $response = $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioCedente->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);

        $this->assertDatabaseHas('carteiras', $carteiraUsuarioCedente);
        $this->assertDatabaseHas('carteiras', $carteiraUsuarioBeneficiario);
    }

    /** @test */
    public function usuario_sem_saldo_suficiente_nao_pode_efetuar_transferencia()
    {
        $usuarioCedente = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioCedente = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioCedente->id, 'saldo' => 50])
            ->toArray();

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioBeneficiario = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0])
            ->toArray();

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioCedente->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);

        $this->assertDatabaseHas('carteiras', $carteiraUsuarioCedente);
        $this->assertDatabaseHas('carteiras', $carteiraUsuarioBeneficiario);
    }

    /** @test */
    public function a_transacao_e_revertida_caso_nao_autorizada()
    {
        Http::fake([
            TransacaoEnum::URL_AUTORIZACAO_TRANSACAO => Http::response(['message' => 'Nao Autorizada'])
        ]);

        $usuarioCedente = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioCedente = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioCedente->id, 'saldo' => 100])
            ->toArray();

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioBeneficiario = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0])
            ->toArray();

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioCedente->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);

        $this->assertDatabaseHas('carteiras', $carteiraUsuarioCedente);
        $this->assertDatabaseHas('carteiras', $carteiraUsuarioBeneficiario);
    }

    /** @test */
    public function notificacao_enviada_ao_final_de_uma_transacao_sucedida()
    {
        Queue::fake();

        Http::fake([
            TransacaoEnum::URL_AUTORIZACAO_TRANSACAO => Http::response(['message' => TransacaoEnum::MENSAGEM_AUTORIZACAO_TRANSACAO]),
            TransacaoEnum::URL_NOTIFICACAO_TRANSACAO => Http::response(['message' => TransacaoEnum::MENSAGEM_NOTIFICACAO_TRANSACAO])
        ]);

        $usuarioCedente = factory(\App\Model\Usuario::class)->create();

        factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioCedente->id, 'saldo' => 100]);

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0]);

        $response = $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioCedente->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(201);

        $notificacao = json_decode($response->content(), true);

        Queue::assertPushed(NotificarTransferencia::class, function ($job) use ($notificacao) {
            return $job->transacao->id === $notificacao['TransactionID'];
        });
    }
}
