<?php

namespace app\commands;

use app\models\Constants;
use app\models\Transfer;
use app\models\TransferStatus;
use app\models\User;
use app\models\Wallet;
use app\utils\NumberFormatUtils;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Transaction;
use yii\mutex\FileMutex;

class ExecTransferController extends Controller
{
    private int $idStatusDone;
    private int $idStatusError;
    private const MUTEX_NAME = 'exec-transfer';

    public function actionIndex(): int
    {
        $mutex = new FileMutex();

        if (!$mutex->acquire(self::MUTEX_NAME)) {
            Yii::error(sprintf('%s command is already running', self::MUTEX_NAME), 'mutex');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->getStatuses();
        $this->makeTransfers();
        $this->addTransfersStatisticInCache();

        $mutex->release(self::MUTEX_NAME);
        return ExitCode::OK;
    }

    private function makeTransfers(): void
    {
        Yii::info('Execution of transfers was started', 'transfers');
        $transfers = Transfer::getTransfersForExecute();

        foreach ($transfers as $transfer) {
            $this->makeOneTransfer($transfer);
        }
        Yii::info('Execution of transfers was ended', 'transfers');
    }

    private function makeOneTransfer(Transfer $transfer): void
    {

        /** @var Transaction $transaction */
        $transaction = Wallet::getDb()->beginTransaction();

        try {
            $senderWallet = $transfer->senderWallet;
            $recipientWallet = $transfer->recipientWallet;

            $senderWallet->amount = NumberFormatUtils::formatAmount(
                $senderWallet->amount - $transfer->amount
            );

            $recipientWallet->amount = NumberFormatUtils::formatAmount(
                $recipientWallet->amount + $transfer->amount
            );

            $transfer->id_status = $this->idStatusDone;

            if (!$senderWallet->save() || !$recipientWallet->save() || !$transfer->save()) {
                Yii::error(
                    sprintf(
                        'Transfer #%s failed - error while saving entities to database: senderWallet errors - %s, recipientWallet errors - %s, transfer errors - %s',
                        $transfer->id,
                        json_encode($senderWallet->errors, JSON_THROW_ON_ERROR),
                        json_encode($recipientWallet->errors, JSON_THROW_ON_ERROR),
                        json_encode($transfer->errors, JSON_THROW_ON_ERROR)
                    ),
                    'transfers'
                );
                $transaction->rollBack();
            }

            $transaction->commit();

        } catch (Throwable $e) {
            Yii::error(sprintf(
                'Transfer #%s was failed, the reason was: %s, stacktrace: %s',
                $transfer->id, $e->getMessage(), $e->getTraceAsString()
            ), 'transfers');
            $transfer->id_status = $this->idStatusError;
            $transaction->rollBack();
        }

        Yii::info(sprintf('Transfer #%s was done', $transfer->id), 'transfers');
    }

    private function addTransfersStatisticInCache(): void
    {
        $lastDoneTransfers = User::getLastDoneTransferForSender();
        Yii::$app->cache->set(Constants::CACHE_KEY_TRANSFER_DONE_STATISTIC, $lastDoneTransfers);
    }

    private function getStatuses(): void
    {
        $statuses = TransferStatus::getIdsByTitles([TransferStatus::DONE, TransferStatus::ERROR]);
        $this->idStatusDone = $statuses[TransferStatus::DONE]['id'];
        $this->idStatusError = $statuses[TransferStatus::ERROR]['id'];
    }
}