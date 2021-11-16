<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper; 
use common\models\Marca;

/* @var $this yii\web\View */
/* @var $model common\models\Modelo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>

    <?= Select2::widget([
        'model' => $model,
        'attribute' => 'idMarca',
        'data' =>  ArrayHelper::map(Marca::find()->all(), 'id', 'nome'),
       
    ]); ?>


    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
