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
            Wallet::getDb()->transaction(function () use ($transfer) {
                $senderWallet = $transfer->senderWallet;
                $recipientWallet = $transfer->recipientWallet;

                Yii::info(sprintf(
                    'transfer #%s start: amount sender: %s, recipient: %s',
                    $transfer->id,
                    $senderWallet->amount, $recipientWallet->amount
                ), 'transfers');

                $senderWallet->amount =
                    number_format($senderWallet->amount - $transfer->amount, 2);
                
                $recipientWallet->amount =
                    number_format($recipientWallet->amount + $transfer->amount, 2);

                Yii::info(sprintf(
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
            return;
        }

        Yii::info(sprintf('Transfer #%s was done', $transfer->id), 'transfers');
    }

    protected function addTransfersStatisticInCache() {

    }
}