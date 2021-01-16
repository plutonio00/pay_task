<?php
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/* @var array $transfer_statistic */
?>

    <h1>Transfer statistic</h1>

    <h3 class="mt-2">Statistics of completed transfers for all users</h3>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $transfer_statistic,
        'pagination' => [
            'pageSize' => 20
        ],
    ]),
    'emptyText' => 'There is no transfers yet',
    'columns' => [
        'u.id' => 'id_user',
        'first_name',
        'last_name',
        'login',
        'email',
        't.id' => 'id_transfer',
        'id_sender',
        'id_sender_wallet',
        'id_recipient',
        'id_recipient_wallet',
        'amount',
        'exec_time',
    ],
])
?>