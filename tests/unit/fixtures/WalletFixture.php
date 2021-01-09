<?php


namespace tests\unit\fixtures;

use app\models\Wallet;
use yii\test\ActiveFixture;

class WalletFixture extends ActiveFixture
{
    public $modelClass = Wallet::class;
    public $dataFile = '@tests/unit/fixtures/data/wallet.php';
    public $depends = [UserFixture::class];
}