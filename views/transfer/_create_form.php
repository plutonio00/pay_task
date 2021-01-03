<?php

use kartik\datetime\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transfer */
/* @var $user app\models\User */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="transfer-form">

    <?php $form = ActiveForm::begin([
        'id' => 'create-transfer-form',
        'action' => '/transfer/create',
        'layout' => 'horizontal',
        'enableAjaxValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
        'options' => [
            'class' => 'mt create-form',
            'data-entity-name' => 'transfer',
        ]
    ]); ?>

    <h4 class="mt col-lg-offset-1">Add new transfer</h4>

    <?= $form->field($model, 'id_sender_wallet')->dropDownList($user->getWalletsArray()) ?>
    <?= $form->field($model, 'id_recipient_wallet')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'exec_time')->widget(DateTimePicker::class, [
        'name' => 'dp_1',
        'convertFormat' => true,
        'pluginOptions' => [
            'format' => 'dd.MM.yyyy HH:00',
            'autoclose' => true,
            'weekStart' => 1,
            'startDate' => date('Y-m-d'),
            'todayBtn' => true,
            'minDate' => date('Y-m-d'),
            'minView' => 1,
        ]
    ]) ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Add transfer', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
