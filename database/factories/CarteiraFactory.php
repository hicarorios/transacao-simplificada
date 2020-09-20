<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Model\Carteira::class, function (Faker $faker) {
    return [
        'usuario_id' => factory(\App\Model\Usuario::class),
        'saldo' => $this->faker->randomFloat(2, 10, 100),
    ];
});
