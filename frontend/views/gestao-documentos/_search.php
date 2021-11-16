<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GestaoDocumentosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="condutor-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'idUsuario') ?>

    <?= $form->field($model, 'idEmpresa') ?>

    <?php // echo $form->field($model, 'idVeiculo') ?>

    <?php // echo $form->field($model, 'fotoMotorista') ?>

    <?php // echo $form->field($model, 'regiao') ?>

    <?php // echo $form->field($model, 'lugares') ?>

    <?php // echo $form->field($model, 'dataNascimento') ?>

    <?php // echo $form->field($model, 'alvara') ?>

    <?php // echo $form->field($model, 'inscricaoMunicipal') ?>

    <?php // echo $form->field($model, 'cpf') ?>

    <?php // echo $form->field($model, 'rg') ?>

    <?php // echo $form->field($model, 'orgaoEmissor') ?>

    <?php // echo $form->field($model, 'lat') ?>

    <?php // echo $form->field($model, 'lng') ?>

    <?php // echo $form->field($model, 'nit') ?>

    <?php // echo $form->field($model, 'endereco') ?>

    <?php // echo $form->field($model, 'bairro') ?>

    <?php // echo $form->field($model, 'telefone') ?>

    <?php // echo $form->field($model, 'celularMonitor') ?>

    <?php // echo $form->field($model, 'telefoneWhatsapp') ?>

    <?php // echo $form->field($model, 'telefone2') ?>

    <?php // echo $form->field($model, 'celular') ?>

    <?php // echo $form->field($model, 'celular2') ?>

    <?php // echo $form->field($model, 'telefoneMonitor') ?>

    <?php // echo $form->field($model, 'telefoneMonitorWhatsapp') ?>

    <?php // echo $form->field($model, 'telefoneWhatsapp2') ?>

    <?php // echo $form->field($model, 'celularWhatsapp') ?>

    <?php // echo $form->field($model, 'celularWhatsapp2') ?>

    <?php // echo $form->field($model, 'celularMonitorWhatsapp') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'cnhRegistro') ?>

    <?php // echo $form->field($model, 'numeroApolice') ?>

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

    <?php // echo $form->field($model, 'nomeMonitor') ?>

    <?php // echo $form->field($model, 'rgMonitor') ?>

    <?php // echo $form->field($model, 'cpfMonitor') ?>

    <?php // echo $form->field($model, 'minKmDia') ?>

    <?php // echo $form->field($model, 'maxKmDia') ?>

    <?php // echo $form->field($model, 'maxViagensDia') ?>

    <?php // echo $form->field($model, 'numeroResidencia') ?>

    <?php // echo $form->field($model, 'cep') ?>

    <?php // echo $form->field($model, 'complementoResidencia') ?>

    <?php // echo $form->field($model, 'tipoLogradouro') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
