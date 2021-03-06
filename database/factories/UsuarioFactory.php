<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Usuario;
use Faker\Generator as Faker;
use Illuminatse\Support\Str;

$factory->define(Usuario::class, function (Faker $faker) {
    return [
        'nome' => $faker->name,
        'cpf-cnpj' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'senha' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'tipo' => Usuario::TIPO_USUARIO,
    ];
});
