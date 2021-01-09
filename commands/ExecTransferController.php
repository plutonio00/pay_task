<?php

namespace app\commands;

use app\models\Transfer;
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

    protected function makeOneTransfer($transfer) {

    }

    protected function addTransfersStatisticInCache() {

    }
}