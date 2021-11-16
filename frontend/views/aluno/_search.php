<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AlunoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aluno-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'idEscola') ?>

    <?= $form->field($model, 'dataNascimento') ?>

    <?= $form->field($model, 'nomeMae') ?>

    <?php // echo $form->field($model, 'nomePai') ?>

    <?php // echo $form->field($model, 'RA') ?>

    <?php // echo $form->field($model, 'endereco') ?>

    <?php // echo $form->field($model, 'lat') ?>

    <?php // echo $form->field($model, 'lng') ?>

    <?php // echo $form->field($model, 'modalidadeBeneficio') ?>

    <?php // echo $form->field($model, 'cartaoPasse') ?>

    <?php // echo $form->field($model, 'horarioEntrada') ?>

    <?php // echo $form->field($model, 'horarioSaida') ?>

    <?php // echo $form->field($model, 'distanceEscola') ?>

    <?php // echo $form->field($model, 'barreiraFisica') ?>

    <?php // echo $form->field($model, 'idRgAluno') ?>

    <?php // echo $form->field($model, 'idComprovanteEndereco') ?>

    <?php // echo $form->field($model, 'idRgResponsavel') ?>

    <?php // echo $form->field($model, 'idDeclaracaoVizinhos') ?>

    <?php // echo $form->field($model, 'idLaudoMedico') ?>

    <?php // echo $form->field($model, 'idTransporteEspecialAdaptado') ?>

    <?php // echo $form->field($model, 'idDeclaracaoInexistenciaVaga') ?>

    <?php // echo $form->field($model, 'telefoneResidencial') ?>

    <?php // echo $form->field($model, 'telefoneResidencial2') ?>

    <?php // echo $form->field($model, 'telefoneCelular') ?>

    <?php // echo $form->field($model, 'telefoneCelular2') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
