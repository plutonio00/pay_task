<?php

use app\models\TransferStatus;
use app\models\User;
use tests\unit\fixtures\Constants;

$transfers = [];
$faker = Faker\Factory::create();

$users = User::getUsersAndWallets()->all();

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

    $execTime = $i > $halfTransferCount ?
        $faker->dateTimeBetween('now', '+10 days') : $faker->dateTimeBetween('-10 days', 'now');

    /** @var User $sender */
    $sender = $faker->randomElement($users);
    /** @var User $recipient */
    $recipient = $faker->randomElement($users);

    $senderWallets = $sender->wallets;
    $senderWalletId = $faker->randomElement($senderWallets)->id;

    if ($sender->id === $recipient->id) {
        $senderWalletIndex = array_search(
            $senderWalletId,
            array_column($senderWallets, 'id'),
            true
        );
        unset($senderWallets[$senderWalletIndex]);
        $recipientWallets = $senderWallets;
    }
    else {
        $recipientWallets = $recipient->wallets;
    }

    $transfers[] = [
        'id_sender' => $sender->id,
        'id_recipient' => $recipient->id,
        'id_sender_wallet' => $senderWalletId,
        'id_recipient_wallet' => $faker->randomElement($recipientWallets)->id,
        'amount' => $faker->randomFloat(2, 50, 100),
        'exec_time' => $execTime->format(Constants::DATE_TIME_FORMAT),
        'id_status' => $i > $halfTransferCount ? $inProgressStatusId : $faker->randomElement($statusIds),
        'created_at' => $createdAt->format(Constants::DATE_TIME_FORMAT),
        'updated_at' => $updatedAt,
    ];
}

return $transfers;
