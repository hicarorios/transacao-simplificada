<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Transacao;
use App\Model\Usuario;
use Faker\Generator as Faker;

$factory->define(Transacao::class, function (Faker $faker) {
    return [
        'cedente_id' => factory(Usuario::class),
        'beneficiario_id' => factory(Usuario::class),
        'valor' => $this->faker->randomFloat(2, 10, 100),
        'status' => Transacao::STATUS_TRANSFERIDO,
        'mensagem' => $faker->text(),
    ];
});
