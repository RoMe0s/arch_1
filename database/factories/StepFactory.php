<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Game\Infrastructure\Persistance\Eloquent\{
    Step,
    Game,
    User
};

/** @var Factory $factory */

$factory->define(Step::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'game_id' => function () {
            return factory(Game::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'coordinate_x' => 0,
        'coordinate_y' => 0
    ];
});
