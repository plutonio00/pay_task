<?php

namespace tests\unit\fixtures;

use app\models\TransferStatus;
use yii\test\ActiveFixture;

class TransferStatusFixture extends ActiveFixture
{
    public $modelClass = TransferStatus::class;
    public $dataFile = '@tests/unit/fixtures/data/transfer_status.php';

}