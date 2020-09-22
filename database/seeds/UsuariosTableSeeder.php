<?php

use App\Model\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        factory(App\Model\Usuario::class, 2)->create(['tipo' => Usuario::TIPO_USUARIO])->each(function ($usuario) {
            $usuario->carteira()->save(factory(App\Model\Carteira::class)
                ->make(['usuario_id' => $usuario->id]));
        });
    }
}
