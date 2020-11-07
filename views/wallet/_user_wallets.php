<?php

use app\models\WalletForm;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var ActiveDataProvider $wallets */
/* @var WalletForm $wallet_form */

echo $this->render('_form', ['model' => $wallet_form]);

if ($wallets) {
    echo GridView::widget([
        'dataProvider' => $wallets,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            'amount',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
}
else {
    echo '<p class="mt">You haven\'t any wallets yet</p>';
}

