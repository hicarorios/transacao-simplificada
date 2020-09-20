<?php

namespace Tests\Feature;

use App\Service\TransacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ManipularTransacaoTest extends TestCase
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
        $usuarioPagador = factory(\App\Model\Usuario::class)->create();

        factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioPagador->id, 'saldo' => 100]);

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0]);

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('carteiras', [
            'usuario_id' => $usuarioPagador->id,
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
        $usuarioPagador = factory(\App\Model\Usuario::class)
            ->create(['tipo' => \App\Model\Usuario::TIPO_LOJISTA]);

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);
    }

    /** @test */
    public function usuario_sem_saldo_nao_pode_efetuar_transferencia()
    {
        $usuarioPagador = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioPagador = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioPagador->id, 'saldo' => 0])
            ->toArray();

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioBeneficiario = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0])
            ->toArray();

        $response = $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);

        $this->assertDatabaseHas('carteiras', $carteiraUsuarioPagador);
        $this->assertDatabaseHas('carteiras', $carteiraUsuarioBeneficiario);
    }

    /** @test */
    public function usuario_sem_saldo_suficiente_nao_pode_efetuar_transferencia()
    {
        $usuarioPagador = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioPagador = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioPagador->id, 'saldo' => 50])
            ->toArray();

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioBeneficiario = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0])
            ->toArray();

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);

        $this->assertDatabaseHas('carteiras', $carteiraUsuarioPagador);
        $this->assertDatabaseHas('carteiras', $carteiraUsuarioBeneficiario);
    }

    /** @test */
    public function a_transacao_e_revertida_caso_nao_autorizada()
    {
        $usuarioPagador = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioPagador = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioPagador->id, 'saldo' => 100])
            ->toArray();

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $carteiraUsuarioBeneficiario = factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioBeneficiario->id, 'saldo' => 0])
            ->toArray();

        Http::fake([
            TransacaoService::URL_AUTORIZACAO_TRANSACAO => Http::response(['message' => 'Nao Autorizada'])
        ]);

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);

        $this->assertDatabaseHas('carteiras', $carteiraUsuarioPagador);
        $this->assertDatabaseHas('carteiras', $carteiraUsuarioBeneficiario);
    }
}
