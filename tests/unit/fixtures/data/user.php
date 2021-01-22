<?php

use tests\unit\fixtures\Constants;

$users = [];
$faker = Faker\Factory::create();

for($i = 0; $i < Constants::USER_COUNT; $i++) {

    $createdAt = $faker->dateTime;

    $updatedAt = $faker->dateTimeBetween($createdAt, '+30 days')
        ->format(Constants::DATE_TIME_FORMAT);

    $firstName = $faker->unique()->firstName;

    $users[] = [
        'first_name' => $firstName,
        'last_name' => $faker->lastName,
        'is_male' => $faker->numberBetween(0, 1),
        'login' => lcfirst($firstName),
        'email' => $faker->unique()->email,
        'password' => Yii::$app->getSecurity()->generatePasswordHash('test_pass'),
        'created_at' => $createdAt->format(Constants::DATE_TIME_FORMAT),
        'updated_at' => $updatedAt,
    ];
}

return $users;
