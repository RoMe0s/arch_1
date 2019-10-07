<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Game\Infrastructure\Persistance\Eloquent\{
    Game,
    User
};

/** @var Factory $factory */

$factory->define(Game::class, function (Faker $faker) {
    $dateTime = $faker->dateTime;
    $startedAt = $dateTime->format('Y-m-d H:i:s');

    $dateTime->add(new DateInterval('P1D'));
    $endedAt = $dateTime->format('Y-m-d H:i:s');

    return [
        'id' => $faker->uuid,
        'owner_id' => function () {
            return factory(User::class)->create()->id;
        },
        'owner_name' => $faker->name,
        'competitor_name' => $faker->name,
        'started_at' => $startedAt,
        'ended_at' => $endedAt
    ];
});
