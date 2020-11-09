<?php

use app\models\Wallet;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var ActiveDataProvider $user_wallets */
/* @var Wallet $wallet */

echo $this->render('_form', ['model' => $wallet]);

if ($user_wallets) {
    echo GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $user_wallets
        ]),
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
}

