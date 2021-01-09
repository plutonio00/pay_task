<?php


namespace tests\unit\fixtures;

use app\models\Transfer;
use yii\test\ActiveFixture;

class TransferFixture extends ActiveFixture
{
    public $modelClass = Transfer::class;
    public $dataFile = '@tests/unit/fixtures/data/transfer.php';
    public $depends = [UserFixture::class, WalletFixture::class, TransferStatusFixture::class];
}