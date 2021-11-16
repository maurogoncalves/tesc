<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CondutorRotaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="condutor-rota-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idCondutor') ?>

    <?= $form->field($model, 'descricao') ?>

    <?= $form->field($model, 'turno') ?>

    <?= $form->field($model, 'sentido') ?>

    <?php // echo $form->field($model, 'entrada') ?>

    <?php // echo $form->field($model, 'saida') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
