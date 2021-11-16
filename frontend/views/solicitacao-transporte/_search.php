<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SolicitacaoTransporteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="solicitacao-transporte-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idAluno') ?>

    <?= $form->field($model, 'idEscola') ?>

    <?= $form->field($model, 'data') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'justificativaBarreiraFisica') ?>

    <?php // echo $form->field($model, 'modalidadeBeneficio') ?>

    <?php // echo $form->field($model, 'cartaoPasseEscolar') ?>

    <?php // echo $form->field($model, 'cartaoValeTransporte') ?>

    <?php // echo $form->field($model, 'barreiraFisica') ?>

    <?php // echo $form->field($model, 'distanciaEscola') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
