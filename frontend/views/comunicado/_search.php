<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ComunicadoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comunicado-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'data') ?>

    <?= $form->field($model, 'idAluno') ?>

    <?= $form->field($model, 'enviadoPor') ?>

    <?= $form->field($model, 'idCondutor') ?>

    <?php // echo $form->field($model, 'idJustificativa') ?>

    <?php // echo $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'condutorCiente') ?>

    <?php // echo $form->field($model, 'responsavelCiente') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
