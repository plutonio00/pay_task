<?php

use app\models\User;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use app\models\Wallet;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var ActiveDataProvider $dataProvider */
/* @var Wallet $wallet */
/* @var User $user */

echo $this->render('/wallet/_create_form', ['model' => $wallet]);

Pjax::begin([
    'id' => 'wallet-pjax-grid-view',
    'enablePushState' => false,
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'emptyText' => 'You haven\'t any wallets',
    'columns' => [
        ['class' => SerialColumn::class],
        'id',
        'title',
        'amount',
        'created_at',
        'updated_at',
        [
            'class' => ActionColumn::class,
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
        'data-pagination-url' => '/user/' . $user->login,
        'data-page-count' => $dataProvider->pagination->pageCount,
        'data-current-page' => $dataProvider->pagination->page,
        'class' => 'mt-2 entity-grid-view',
        'id' => 'wallet-grid-view'
    ]
]);
Pjax::end();
