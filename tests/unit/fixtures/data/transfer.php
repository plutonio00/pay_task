<?php

use app\models\TransferStatus;
use app\models\User;
use app\models\Wallet;
use tests\unit\fixtures\Constants;

$transfers = [];
$faker = Faker\Factory::create();
$userIds = User::find()->select('id')->asArray()->column();
$walletIds = Wallet::find()->select('id')->asArray()->column();

$inProgressStatusId = TransferStatus::getIdByTitle(TransferStatus::IN_PROGRESS);

$statusIds = TransferStatus::find()
    ->select('id')
    ->where('id <> ' . $inProgressStatusId)
    ->asArray()
    ->column();

$halfTransferCount = Constants::TRANSFER_COUNT / 2;

for ($i = 0; $i < Constants::TRANSFER_COUNT; $i++) {

    $createdAt = $faker->dateTime;

    $updatedAt = $faker->dateTimeBetween($createdAt, '+30 days')
        ->format(Constants::DATE_TIME_FORMAT);

    $currentWallets = $faker->randomElements($walletIds, 2, false);
    $execTime = $i > $halfTransferCount ?
        $faker->dateTimeBetween('now', '+10 days') : $faker->dateTimeBetween('-10 days', 'now');

    $idStatus =

    $transfers[] = [
        'id_sender' => $faker->randomElement($userIds),
        'id_recipient' => $faker->randomElement($userIds),
        'id_sender_wallet' => $currentWallets[0],
        'id_recipient_wallet' => $currentWallets[1],
        'amount' => $faker->randomFloat(2, 50, 100),
        'exec_time' => $execTime->format(Constants::DATE_TIME_FORMAT),
        'id_status' => $i > $halfTransferCount ? $inProgressStatusId : $faker->randomElement($statusIds),
        'created_at' => $createdAt->format(Constants::DATE_TIME_FORMAT),
        'updated_at' => $updatedAt,
    ];
}

return $transfers;