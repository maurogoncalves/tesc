<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Condutor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idUsuario')->textInput() ?>

    <?= $form->field($model, 'idEmpresa')->textInput() ?>

    <?= $form->field($model, 'idVeiculo')->textInput() ?>

    <?= $form->field($model, 'fotoMotorista')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'regiao')->textInput() ?>

    <?= $form->field($model, 'dataNascimento')->textInput() ?>

    <?= $form->field($model, 'alvara')->textInput() ?>

    <?= $form->field($model, 'inscricaoMunicipal')->textInput() ?>

    <?= $form->field($model, 'cpf')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rg')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'orgaoEmissor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lng')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'endereco')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bairro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'celularMonitor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefoneWhatsapp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefone2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'celular')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'celular2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefoneMonitor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefoneMonitorWhatsapp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefoneWhatsapp2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'celularWhatsapp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'celularWhatsapp2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'celularMonitorWhatsapp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cnhRegistro')->textInput() ?>

    <?= $form->field($model, 'numeroApolice')->textInput() ?>

    <?= $form->field($model, 'cnhValidade')->textInput() ?>

    <?= $form->field($model, 'dataInicioContrato')->textInput() ?>

    <?= $form->field($model, 'dataFimContrato')->textInput() ?>

    <?= $form->field($model, 'tipoContrato')->textInput() ?>

    <?= $form->field($model, 'valorPagoKmViagem')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idCNHCondutor')->textInput() ?>

    <?= $form->field($model, 'idComprovanteEndereco')->textInput() ?>

    <?= $form->field($model, 'idCRLV')->textInput() ?>

    <?= $form->field($model, 'idVistoriaEstadual')->textInput() ?>

    <?= $form->field($model, 'idVstoriaMunicipal')->textInput() ?>

    <?= $form->field($model, 'idApoliceSeguro')->textInput() ?>

    <?= $form->field($model, 'idContrato')->textInput() ?>

    <?= $form->field($model, 'nomeMonitor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rgMonitor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cpfMonitor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'minKmDia')->textInput() ?>

    <?= $form->field($model, 'maxKmDia')->textInput() ?>

    <?= $form->field($model, 'maxViagensDia')->textInput() ?>

    <?= $form->field($model, 'numeroResidencia')->textInput() ?>

    <?= $form->field($model, 'cep')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'complementoResidencia')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipoLogradouro')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
