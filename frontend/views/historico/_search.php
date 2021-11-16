<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HistoricoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="historico-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idCondutorRota') ?>

    <?= $form->field($model, 'idCondutor') ?>

    <?= $form->field($model, 'idVeiculo') ?>

    <?= $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'checkIn') ?>

    <?php // echo $form->field($model, 'checkOut') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
