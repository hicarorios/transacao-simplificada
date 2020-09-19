<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function usuario_deve_ser_unico()
    {
        $this->expectException('\Illuminate\Database\QueryException');

       factory(\App\Model\Usuario::class, 2)->create(
            ['email' => 'teste@teste.com', 'cpf-cnpj' => '02454854220']
        );
    }
}
