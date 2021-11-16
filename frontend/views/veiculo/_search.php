<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VeiculoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="veiculo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idModelo') ?>

    <?= $form->field($model, 'placa') ?>

    <?= $form->field($model, 'capacidade') ?>

    <?= $form->field($model, 'combustivel') ?>

    <?php // echo $form->field($model, 'dataVistoriaEstadual') ?>

    <?php // echo $form->field($model, 'dataVistoriaMunicipal') ?>

    <?php // echo $form->field($model, 'dataVencimenoSeguro') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
