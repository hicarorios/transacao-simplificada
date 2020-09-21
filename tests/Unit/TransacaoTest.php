<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransacaoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function transacao_tem_cedente()
    {
        $usuario = factory(\App\Model\Transacao::class)->create();

        $this->assertInstanceOf(\App\Model\Usuario::class, $usuario->cedente);
    }

     /** @test */
     public function transacao_tem_beneficiario()
     {
         $usuario = factory(\App\Model\Transacao::class)->create();

         $this->assertInstanceOf(\App\Model\Usuario::class, $usuario->beneficiario);
     }
}
