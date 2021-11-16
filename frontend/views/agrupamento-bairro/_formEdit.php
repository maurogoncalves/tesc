<?php

use common\models\AgrupamentoBairro;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Bairro;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\AgrupamentoBairro */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">
    <?php $form = ActiveForm::begin([
        'encodeErrorSummary' => false,
        'errorSummaryCssClass' => 'help-block',
        ]); ?>
 
    <div class="row">
        <div class="col-md-8">   
        <?= $form->field($model, 'nome')->textInput(['maxlength' => 10, 'disabled' => true])->label('Bairro'); ?>

        </div>
        <div class="col-md-4">
            <?=
              $form->field($model, 'agrupamento')
              ->dropDownList(
                  AgrupamentoBairro::ARRAY_BAIRRO,          
                  ['prompt'=>'Selecione']    
              )

        ?>
       </div>
    </div>

  <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
