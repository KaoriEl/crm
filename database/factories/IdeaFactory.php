<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Idea::class, function (Faker $faker) {
    return [
        'text' => $faker->text,
    ];
});
