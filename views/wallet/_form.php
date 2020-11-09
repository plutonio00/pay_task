<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Wallet */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="wallet-form">

    <?php Pjax::begin([
        'enablePushState' => false,
    ]) ?>

    <?php $form = ActiveForm::begin([
        'action' => ['/wallet/add'],
        'id' => 'create-wallet-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
        'options' => [
            'class' => 'mt',
            'data-pjax' => true,
        ]
    ]); ?>

    <h4 class="mt col-lg-offset-1">Add new wallet</h4>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Add wallet', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end() ?>

</div>
