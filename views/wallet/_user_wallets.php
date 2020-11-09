<?php

use app\models\Wallet;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var ActiveDataProvider $user_wallets */
/* @var Wallet $wallet */

echo $this->render('_form', ['model' => $wallet]);

if ($user_wallets) {
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $user_wallets
        ]),
        'emptyText' => 'You haven\'t any wallets yet',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            'amount',
            'created_at',
            'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'options' => [
            'class' => 'mt-2',
        ]
    ]);
    Pjax::end();
}

