<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Log */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'data')->textInput() ?>

    <?= $form->field($model, 'acao')->textInput() ?>

    <?= $form->field($model, 'referencia')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coluna')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idUsuario')->textInput() ?>

    <?= $form->field($model, 'idAlunoTable')->textInput() ?>

    <?= $form->field($model, 'idEscolaTable')->textInput() ?>

    <?= $form->field($model, 'idSolicitacaoTransporteTable')->textInput() ?>

    <?= $form->field($model, 'idSolicitacaoCreditoTable')->textInput() ?>

    <?= $form->field($model, 'idCondutorRotaTable')->textInput() ?>

    <?= $form->field($model, 'idOcorrenciaTable')->textInput() ?>

    <?= $form->field($model, 'idCondutorTable')->textInput() ?>

    <?= $form->field($model, 'idVeiculoTable')->textInput() ?>

    <?= $form->field($model, 'idMarcaTable')->textInput() ?>

    <?= $form->field($model, 'idModeloTable')->textInput() ?>

    <?= $form->field($model, 'idUsuarioTable')->textInput() ?>

    <?= $form->field($model, 'idJustificativaTable')->textInput() ?>

    <?= $form->field($model, 'idReciboPagamentoAutonomoTable')->textInput() ?>

    <?= $form->field($model, 'idNecessidadesEspeciaisTable')->textInput() ?>

    <?= $form->field($model, 'idConfiguracaoTable')->textInput() ?>

    <?= $form->field($model, 'idEmpresaTable')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
