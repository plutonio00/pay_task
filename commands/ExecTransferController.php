<?php

namespace app\commands;

use app\models\Transfer;
use app\models\Wallet;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class ExecTransferController extends Controller
{
    public function actionIndex()
    {
        $this->makeTransfers();
        $this->addTransfersStatisticInCache();
        return ExitCode::OK;
    }

    protected function makeTransfers() {
        $transfers = Transfer::getTransfersInProgress();

        foreach ($transfers as $transfer) {
            $this->makeOneTransfer($transfer);
        }
    }

    protected function makeOneTransfer(Transfer $transfer) {

        try {
            Wallet::getDb()->transaction(function ($db) use ($transfer) {
                $senderWallet = $transfer->senderWallet;
                $recipientWallet = $transfer->recipientWallet;

                $senderWallet->amount -= $transfer->amount;
                $recipientWallet->amount += $transfer->amount;

                $senderWallet->save();
                $recipientWallet->save();
            });
        } catch (Throwable $e) {

        }
    }

    protected function addTransfersStatisticInCache() {

    }
}