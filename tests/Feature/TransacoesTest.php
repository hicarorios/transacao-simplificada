<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $response = $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(201);
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

        factory(\App\Model\Carteira::class)
            ->create(['usuario_id' => $usuarioPagador->id, 'saldo' => 0]);

        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $response = $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])->assertStatus(400);
    }

      /** @test */
      public function usuario_sem_saldo_suficiente_nao_pode_efetuar_transferencia()
      {
          $usuarioPagador = factory(\App\Model\Usuario::class)->create();

          factory(\App\Model\Carteira::class)
              ->create(['usuario_id' => $usuarioPagador->id, 'saldo' => 50]);

          $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

          $response = $this->json('POST', 'api/transaction', [
              'value' => 100.00,
              'payer' => $usuarioPagador->id,
              'payee' => $usuarioBeneficiario->id,
          ])->assertStatus(400);
      }
}
