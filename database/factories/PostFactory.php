<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'title'           => $faker->sentence(),
        'text'            => $faker->text(),

        'expires_at'      => $faker->dateTime(now()->addYear(1)),

        'targeting'       => $faker->boolean(10),
        'targeting_to_vk' => $faker->boolean(10),
        'targeting_to_ok' => $faker->boolean(10),
        'targeting_to_fb' => $faker->boolean(10),
        'targeting_to_ig' => $faker->boolean(10),

        'posting'         => $faker->boolean(),
        'posting_to_vk'   => $faker->boolean(),
        'posting_to_ok'   => $faker->boolean(),
        'posting_to_fb'   => $faker->boolean(),
        'posting_to_ig'   => $faker->boolean(),

        'seeding'         => $faker->boolean(),
        'seeding_to_vk'   => $faker->boolean(),
        'seeding_to_ok'   => $faker->boolean(),

        'commenting'      => $faker->boolean(),
    ];
});
