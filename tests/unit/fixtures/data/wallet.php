<?php

use app\models\User;
use tests\unit\fixtures\Constants;

$wallets = [];
$faker = Faker\Factory::create();
$userIds = User::find()->select('id')->asArray()->column();

for($i = 0; $i < Constants::WALLET_COUNT; $i++) {
    $createdAt = $faker->dateTime;

    $updatedAt = $faker->dateTimeBetween($createdAt, '+30 days')
        ->format(Constants::DATE_TIME_FORMAT);

    $wallets[] = [
        'id_user' => $faker->randomElement($userIds),
        'title' => 'wallet_' . $i,
        'amount' => $faker->randomFloat(2, 2000, 5000),
        'created_at' => $createdAt->format(Constants::DATE_TIME_FORMAT),
        'updated_at' => $updatedAt,
    ];
}

return $wallets;