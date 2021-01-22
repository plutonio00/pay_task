<?php

use yii\web\JqueryAsset;

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
$this->registerJsFile('/js/profile.js', ['depends' => JqueryAsset::class]);

?>
<div class="user-view">
    <h1><?= $model->getFullName() ?></h1>

    <div id="profile-tab-wrapper" class="mt">

        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Wallets',
                    'content' => $this->render('/wallet/_wallets', [
                        'wallet' => $wallet,
                        'userWallets' => $model->getWallets(),
                    ]),
                    'active' => true,
                    'options' => [
                        'class' => 'border plr',
                    ]
                ],
                [
                    'label' => 'Transfers',
                     'headerOptions' => [
                        'id' => 'transfers-tab-header',
                    ],
                    'options' => [
                        'class' => 'border plr',
                        'id' => 'transfers-tab-content',
                    ]
                ],
            ],
        ]);
        ?>

    </div>

    <?php Modal::begin([
        'id' => 'entity-actions-modal',
    ]); ?>
    <?php Modal::end(); ?>

</div>
