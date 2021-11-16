<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\NotificacaoEnviadaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notificacao-enviada-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idUsuario') ?>

    <?= $form->field($model, 'data') ?>

    <?= $form->field($model, 'idFirebase') ?>

    <?= $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'texto') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
