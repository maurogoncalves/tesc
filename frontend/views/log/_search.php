<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'data') ?>

    <?= $form->field($model, 'acao') ?>

    <?= $form->field($model, 'referencia') ?>

    <?= $form->field($model, 'tabela') ?>

    <?php // echo $form->field($model, 'coluna') ?>

    <?php // echo $form->field($model, 'antes') ?>

    <?php // echo $form->field($model, 'depois') ?>

    <?php // echo $form->field($model, 'idUsuario') ?>

    <?php // echo $form->field($model, 'idAlunoTable') ?>

    <?php // echo $form->field($model, 'idEscolaTable') ?>

    <?php // echo $form->field($model, 'idSolicitacaoTransporteTable') ?>

    <?php // echo $form->field($model, 'idSolicitacaoCreditoTable') ?>

    <?php // echo $form->field($model, 'idCondutorRotaTable') ?>

    <?php // echo $form->field($model, 'idOcorrenciaTable') ?>

    <?php // echo $form->field($model, 'idCondutorTable') ?>

    <?php // echo $form->field($model, 'idVeiculoTable') ?>

    <?php // echo $form->field($model, 'idMarcaTable') ?>

    <?php // echo $form->field($model, 'idModeloTable') ?>

    <?php // echo $form->field($model, 'idUsuarioTable') ?>

    <?php // echo $form->field($model, 'idJustificativaTable') ?>

    <?php // echo $form->field($model, 'idReciboPagamentoAutonomoTable') ?>

    <?php // echo $form->field($model, 'idNecessidadesEspeciaisTable') ?>

    <?php // echo $form->field($model, 'idConfiguracaoTable') ?>

    <?php // echo $form->field($model, 'idEmpresaTable') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
