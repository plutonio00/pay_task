<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReplenishForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $title string */
?>

<div class="wallet-form">

    <?php $form = ActiveForm::begin([
        'id' => 'wallet-replenish-form',
        'action' => '/wallet/replenish',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-7\">{input}</div>\n<div class=\"col-lg-3\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
        'options' => [
            'class' => 'mt',
            'data-entity-name' => 'wallet',
        ]
    ]); ?>

    <h4 class="mt col-lg-offset-1">
        You want to replenish wallet <span class="text-success"><?= $title ?></span>
    </h4>

    <?= $form->field($model, 'id_wallet')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <?= Html::submitButton('Replenish wallet', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>