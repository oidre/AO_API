<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->email,
        'password' => password_hash('password', PASSWORD_BCRYPT),
    ];
});

$factory->define(App\Module::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'application_object' => $faker->numberBetween($min = 0, $max = 150),
    ];
});
