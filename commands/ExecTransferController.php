<?php

namespace app\commands;

use app\models\Transfer;
use app\models\TransferStatus;
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
        $transfers = Transfer::getTransfersInProgressForPreviousHour();

        foreach ($transfers as $transfer) {
            $this->makeOneTransfer($transfer);
        }
    }

    protected function makeOneTransfer(Transfer $transfer) {

        try {
            Wallet::getDb()->transaction(function ($db) use ($transfer) {
                $senderWallet = $transfer->senderWallet;
                $recipientWallet = $transfer->recipientWallet;

                Yii::error(sprintf(
                    'transfer #%s start: amount sender: %s, recipient: %s',
                    $transfer->id,
                    $senderWallet->amount, $recipientWallet->amount
                ), 'transfers');

                $senderWallet->amount -= $transfer->amount;
                $recipientWallet->amount += $transfer->amount;

                Yii::error(sprintf(
                    'transfer #%s  end: amount sender: %s, recipient: %s',
                    $transfer->id,
                    $senderWallet->amount, $recipientWallet->amount
                ), 'transfers');

                $transfer->id_status = TransferStatus::getIdByTitle(TransferStatus::DONE);

                $senderWallet->save();
                $recipientWallet->save();
                $transfer->save();
            });
        } catch (Throwable $e) {
            Yii::error(sprintf(
                'Transfer #%s was failed, the reason was: %s, stacktrace: %s', $transfer->id, $e->getMessage(), $e->getTraceAsString()
            ), 'transfers');
            $transfer->id_status = TransferStatus::getIdByTitle(TransferStatus::ERROR);
            $transfer->save();
        }
    }

    protected function addTransfersStatisticInCache() {

    }
}