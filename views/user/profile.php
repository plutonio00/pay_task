<?php

use app\models\Transfer;
use app\models\Wallet;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use app\models\User;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this View */

/* @var User $model */
/* @var ArrayDataProvider $dataProvider */
/* @var Wallet $wallet */
/* @var Transfer $transfer */

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
                        'dataProvider' => $dataProvider,
                    ]),
                    'active' => true,
                    'options' => [
                        'class' => 'border plr',
                    ]
                ],
                [
                    'label' => 'Transfers',
                    'content' => $this->render('/transfer/_transfers', [
                        'model' => $transfer,
                        'user' => $model,
                    ]),
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
