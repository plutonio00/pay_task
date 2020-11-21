<?php

/* @var $this yii\web\View */

/* @var app\models\User $model */
/* @var ArrayDataProvider $dataProvider */
/* @var Wallet $wallet */

use app\models\Wallet;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

$this->title = 'Profile';

$this->registerJSFile('/js/common/jquery.js');
$this->registerJsFile('/js/profile.js');

?>
<div class="user-view">
    <h1><?= $model->getFullName() ?></h1>

    <?= Html::a('Log out', '/site/logout', ['class' => 'profile-link']) ?>

    <div class="mt">

        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Wallets',
                    'content' => $this->render('/user/_wallets', [
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
            'options' => [
                'id' => 'profile-tab'
            ]
        ]);
        ?>

    </div>

    <?php Modal::begin([
        'id' => 'entity-actions-modal',
    ]); ?>
    <?php Modal::end(); ?>

</div>
