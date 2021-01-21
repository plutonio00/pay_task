<?php

use yii\grid\SerialColumn;
use app\models\search\WalletSearch;
use app\models\Wallet;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var ActiveDataProvider $user_wallets */
/* @var Wallet $wallet */
/* @var WalletSearch $wallet_search */

echo $this->render('/wallet/_create_form', ['model' => $wallet]);

Pjax::begin([
    'id' => 'wallet-list-grid-view'
]);

echo GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $user_wallets
    ]),
    'filterModel' => $wallet_search,
    'emptyText' => 'You haven\'t any wallets',
    'columns' => [
        ['class' => SerialColumn::class],
        'id',
        'title',
        'amount',
        'created_at',
        'updated_at',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Replenish the balance',
            'template' => '{replenish}',
            'buttons' => [
                'replenish' => function ($url, $dataProvider) {
                    return Html::tag('span', '', [
                        'class' => 'glyphicon glyphicon-credit-card btn-icon replenish-btn',
                        'title' => 'Replenish the balance',
                        'data-id' => $dataProvider['id'],
                    ]);
                }
            ]
        ],
    ],
    'options' => [
        'class' => 'mt-2 entity-grid-view',
    ]
]);
Pjax::end();