<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CondutorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="condutor-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idUsuario') ?>

    <?= $form->field($model, 'idVeiculo') ?>

    <?= $form->field($model, 'dataNascimento') ?>

    <?= $form->field($model, 'alvara') ?>

    <?php // echo $form->field($model, 'inscricaoMunicipal') ?>

    <?php // echo $form->field($model, 'cpf') ?>

    <?php // echo $form->field($model, 'lat') ?>

    <?php // echo $form->field($model, 'lng') ?>

    <?php // echo $form->field($model, 'nit') ?>

    <?php // echo $form->field($model, 'endereco') ?>

    <?php // echo $form->field($model, 'bairro') ?>

    <?php // echo $form->field($model, 'telefone') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'cnhRegistro') ?>

    <?php // echo $form->field($model, 'cnhValidade') ?>

    <?php // echo $form->field($model, 'dataInicioContrato') ?>

    <?php // echo $form->field($model, 'dataFimContrato') ?>

    <?php // echo $form->field($model, 'tipoContrato') ?>

    <?php // echo $form->field($model, 'valorPagoKmViagem') ?>

    <?php // echo $form->field($model, 'idCNHCondutor') ?>

    <?php // echo $form->field($model, 'idComprovanteEndereco') ?>

    <?php // echo $form->field($model, 'idCRLV') ?>

    <?php // echo $form->field($model, 'idVistoriaEstadual') ?>

    <?php // echo $form->field($model, 'idVstoriaMunicipal') ?>

    <?php // echo $form->field($model, 'idApoliceSeguro') ?>

    <?php // echo $form->field($model, 'idContrato') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
