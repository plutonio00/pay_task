<?php

/* @var $this yii\web\View */

/* @var app\models\User $model */
/* @var ArrayDataProvider $dataProvider */
/* @var Wallet $wallet */

use app\models\Wallet;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

$this->title = 'Profile';

?>
<div class="user-view">
    <h1><?= $model->getFullName() ?></h1>

    <?= Html::a('Log out', '/site/logout', ['class' => 'profile-link']) ?>

    <div class="mt">

        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Wallets',
                    'content' => $this->render('/wallet/_user_wallets', [
                        'wallet' => $wallet,
                        'user_wallets' => $model->getWallets(),
                    ]),
                    'active' => true, // указывает на активность вкладки
                    'options' => [
                        'class' => 'border plr',
                    ]
                ],
                [
                    'label' => 'Transfers',
                    //'content' => $this->render('/transfer/_user_transfers'),
                    'options' => [
                        'class' => 'border plr',
                    ]
                ],
            ],
        ]);
        ?>

    </div>

</div>
