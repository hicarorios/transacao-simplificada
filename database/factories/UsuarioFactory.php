<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminatse\Support\Str;

$factory->define(\App\Model\Usuario::class, function (Faker $faker) {
    return [
        'nome' => $faker->name,
        'cpf-cnpj' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'senha' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'tipo' => $faker->randomElement([\App\Model\Usuario::TIPO_USUARIO, \App\Model\Usuario::TIPO_LOJISTA]),
    ];
});
