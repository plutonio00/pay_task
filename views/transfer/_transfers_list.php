<?php

use app\models\Transfer;
use app\models\TransferStatus;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var ActiveDataProvider $dataProvider */

Pjax::begin([
    'id' => 'transfer-pjax-grid-view',
    'enablePushState' => false,
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'emptyText' => 'You haven\'t any transfers',
    'columns' => [
        ['class' => SerialColumn::class],
        'id',
        [
            'attribute' => 'id_sender_wallet',
            'value' => fn(Transfer $transfer) => $transfer->getDisplayWalletDataForOwner('sender'),
        ],
        [
            'attribute' => 'id_recipient_wallet',
            'value' => fn(Transfer $transfer) => $transfer->getDisplayWalletDataForOwner('recipient'),
        ],
        'amount',
        'exec_time',
        [
            'attribute' => 'status',
            'value' => fn(Transfer $transfer) => $transfer->status->title,
        ],
        'created_at',
        'updated_at',
        [
            'class' => ActionColumn::class,
            'header' => 'Actions',
            'template' => '{retry}{cancel}',
            'buttons' => [
                'cancel' => function ($url, Transfer $transfer): string {
                    if ($transfer->status->title === TransferStatus::IN_PROGRESS) {
                        return Html::tag('span', '', [
                            'class' => 'glyphicon glyphicon-remove-circle btn-icon cancel-btn text-danger',
                            'title' => 'Cancel the transfer',
                            'data-id' => $transfer->id,
                            'data-entity-name' => 'transfer'
                        ]);
                    }
                    return '';
                },
                'retry' => function ($url, Transfer $transfer): string {
                    if ($transfer->status->title === TransferStatus::ERROR) {
                        return Html::tag('span', '', [
                            'class' => 'glyphicon glyphicon-repeat btn-icon retry-btn',
                            'title' => 'Retry to make the transfer',
                            'data-id' => $transfer->id,
                        ]);
                    }
                    return '';
                },
            ]
        ],
    ],
    'options' => [
        'data-pagination-url' => '/transfer/get-user-transfers',
        'data-page-count' => $dataProvider->pagination->pageCount,
        'data-current-page' => $dataProvider->pagination->page,
        'class' => 'mt-2 entity-grid-view',
        'id' => 'transfer-grid-view'
    ]
]);
Pjax::end();