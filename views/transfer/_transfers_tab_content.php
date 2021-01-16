<?php

use app\models\Transfer;
use app\models\TransferStatus;
use app\models\User;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var User $user */
/* @var Transfer $model */
/* @var ActiveQuery $transfers */

echo $this->render('/transfer/_create_form', [
    'model' => $model,
    'user' => $user
]);

Pjax::begin([
    'id' => 'transfer-list-grid-view'
]);

echo GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $transfers,
    ]),
    'emptyText' => 'You haven\'t any transfers',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        [
            'attribute' => 'id_sender_wallet',
            'value' => fn(Transfer $data) => $data->getDisplayWalletDataForOwner('sender'),
        ],
        [
            'attribute' => 'id_recipient_wallet',
            'value' => fn(Transfer $data) => $data->getDisplayWalletDataForOwner('recipient'),
        ],
        'amount',
        'exec_time',
        [
            'attribute' => 'status',
            'value' => fn(Transfer $data) => $data->status->title,
        ],
        'created_at',
        'updated_at',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{retry}{cancel}',
            'buttons' => [
                'cancel' => function ($url, Transfer $dataProvider): string {
                    if ($dataProvider->status->title === TransferStatus::IN_PROGRESS) {
                        return Html::tag('span', '', [
                            'class' => 'glyphicon glyphicon-remove-circle btn-icon cancel-btn text-danger',
                            'title' => 'Cancel the transfer',
                            'data-id' => $dataProvider['id'],
                            'data-entity-name' => 'transfer'
                        ]);
                    }
                    return '';
                },
                'retry' => function ($url, Transfer $dataProvider): string {
                    if ($dataProvider->status->title === TransferStatus::ERROR) {
                        return Html::tag('span', '', [
                            'class' => 'glyphicon glyphicon-repeat btn-icon retry-btn',
                            'title' => 'Retry to make the transfer',
                            'data-id' => $dataProvider['id'],
                        ]);
                    }
                    return '';
                },
            ]
        ],
    ],
    'options' => [
        'class' => 'mt-2 entity-grid-view',
    ]
]);
Pjax::end();