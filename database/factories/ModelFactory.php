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

use \Carbon\Carbon;
Carbon::setLocale('id');

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

$factory->define(App\Project::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
    ];
});

$factory->define(App\Date::class, function (Faker\Generator $faker) {
    return [
        'full_date' => Carbon::now()->format('Y-m-d'),
        'month' => Carbon::now()->month,
        'month_name' => Carbon::now()->monthName,
        'year' => Carbon::now()->year,
    ];
});
