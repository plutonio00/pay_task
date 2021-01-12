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

    /**
     * For correct creation of transfers, each user must have at least two accounts.
     * Otherwise, a situation may occur when the user is selected,
     * but he has no accounts to transfer, or the user is selected twice,
     * but he cannot transfer money to his other account.
     */
    $userId = $i < Constants::DOUBLE_USER_COUNT ?
        $userIds[$i % Constants::USER_COUNT] : $faker->randomElement($userIds);

    $wallets[] = [
        'id_user' => $userId,
        'title' => 'wallet_' . $i,
        'amount' => $faker->randomFloat(2, 2000, 5000),
        'created_at' => $createdAt->format(Constants::DATE_TIME_FORMAT),
        'updated_at' => $updatedAt,
    ];
}

return $wallets;