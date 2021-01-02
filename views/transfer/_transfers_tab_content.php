<?php

use app\models\Transfer;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var User $user */
/* @var Transfer $model */

echo $this->render('/transfer/_create_form', [
    'model' => $model,
    'user' => $user
]);

Pjax::begin([
    'id' => 'transfer-list-grid-view'
]);

echo GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $user->getTransfers(),
    ]),
    'emptyText' => 'You haven\'t any transfers',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'id_sender_wallet',
        'id_recipient_wallet',
        'amount',
        'exec_time',
        'transfer_status.title' => 'status',
        'created_at',
        'updated_at',
        [
            'class' => 'yii\grid\ActionColumn',
//            'header' => 'Replenish the balance',
//            'template' => '{replenish}',
            'buttons' => [
//                'replenish' => function ($url, $dataProvider) {
//                    return Html::tag('span', '', [
//                        'class' => 'glyphicon glyphicon-credit-card btn-icon replenish-btn',
//                        'title' => 'Replenish the balance',
//                        'data-id' => $dataProvider['id'],
//                    ]);
//                }
            ]
        ],
    ],
    'options' => [
        'class' => 'mt-2 entity-grid-view',
    ]
]);
Pjax::end();