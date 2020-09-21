<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Carteira;
use App\Model\Usuario;
use Faker\Generator as Faker;

$factory->define(Carteira::class, function (Faker $faker) {
    return [
        'usuario_id' => factory(Usuario::class),
        'saldo' => $this->faker->randomFloat(2, 10, 100),
    ];
});
