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

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
	return [
		'characterID'        => rand(100000, 999999),
		'characterName'      => $faker->name,
		'characterOwnerHash' => str_random(10),
		'corporationID'      => rand(100000, 999999),
		'corporationName'    => $faker->company,
		'corporationTicker'  => strtoupper(str_random(5)),
		'allianceID'         => rand(100000, 999999),
		'allianceName'       => $faker->company,
		'allianceTicker'     => strtoupper(str_random(5)),
		'flags'              => 0,
		'remember_token'     => str_random(10),
	];
});
