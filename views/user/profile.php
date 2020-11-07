<?php

/* @var $this yii\web\View */

/* @var app\models\User $model */
/* @var ArrayDataProvider $dataProvider */
/* @var WalletForm $wallet_form */

use app\models\WalletForm;
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
                        'wallet_form' => $wallet_form,
                        'model' => $model,
                    ]),
                    'active' => true, // указывает на активность вкладки
                    'options' => [
                        'class' => 'border pl',
                    ]
                ],
                [
                    'label' => 'Transfers',
                    //'content' => $this->render('/transfer/_user_transfers'),
                    'options' => [
                        'class' => 'border pl',
                    ]
                ],
            ],
        ]);
        ?>

    </div>

</div>
