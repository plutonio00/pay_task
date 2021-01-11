<?php

use app\models\TransferStatus;
use app\models\User;
use app\models\Wallet;
use tests\unit\fixtures\Constants;

$transfers = [];
$faker = Faker\Factory::create();
$userIds = User::find()->select('id')->asArray()->column();
$walletsData = Wallet::find()->select(['id', 'id_user'])->asArray()->all();

$filterWalletDataByIdUser = function (array $walletsData, $idUser): array {
    $result = [];

    foreach ($walletsData as $item) {
        if($item['id_user'] === $idUser) {
            $result[] = $item['id'];
        }
    }

    return $result;
};

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

    $currentWallets = $faker->randomElements($walletsData, 2, false);
    $execTime = $i > $halfTransferCount ?
        $faker->dateTimeBetween('now', '+10 days') : $faker->dateTimeBetween('-10 days', 'now');

    $idSender = $faker->randomElement($userIds);
    $idRecipient = $faker->randomElement($userIds);

    $senderWalletsIds = $filterWalletDataByIdUser($walletsData, $idSender);
    $recipientWalletsIds = $filterWalletDataByIdUser($walletsData, $idRecipient);

    $transfers[] = [
        'id_sender' => $idSender,
        'id_recipient' => $idRecipient,
        'id_sender_wallet' => $faker->randomElement($senderWalletsIds),
        'id_recipient_wallet' => $faker->randomElement($recipientWalletsIds),
        'amount' => $faker->randomFloat(2, 50, 100),
        'exec_time' => $execTime->format(Constants::DATE_TIME_FORMAT),
        'id_status' => $i > $halfTransferCount ? $inProgressStatusId : $faker->randomElement($statusIds),
        'created_at' => $createdAt->format(Constants::DATE_TIME_FORMAT),
        'updated_at' => $updatedAt,
    ];
}

return $transfers;
