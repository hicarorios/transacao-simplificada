<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManipularTransacaoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_transfere_dinheiro_para_usuario()
    {
        $this->withoutExceptionHandling();

        $usuarioPagador = factory(\App\Model\Usuario::class)->create();
        $usuarioBeneficiario = factory(\App\Model\Usuario::class)->create();

        $this->json('POST', 'api/transaction', [
            'value' => 100.00,
            'payer' => $usuarioPagador->id,
            'payee' => $usuarioBeneficiario->id,
        ])
        ->assertStatus(201);
    }
}
