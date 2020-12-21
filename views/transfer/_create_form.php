<?php
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
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
        'options' => [
            'class' => 'mt create-form',
            'data-entity-name' => 'transfer',
        ]
    ]); ?>

    <h4 class="mt col-lg-offset-1">Add new wallet</h4>

    <?= $form->field($model, 'id_sender_wallet')->dropDownList($user->getWalletsArray()) ?>
    <?= $form->field($model, 'id_recipient_wallet')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'exec_time')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Add transfer', [
                    'class' => 'btn btn-success',
                    'name' => 'submit-btn'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
