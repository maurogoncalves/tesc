<?php
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use yii\helpers\Html; 
use kartik\select2\Select2;
use common\models\TipoLogradouro;


/* @var $this yii\web\View */
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */
?> 
<style type="text/css">

.input-group {
  width: 100%;
}



</style>

<div class="box-body"> 
    <?php $form = ActiveForm::begin([
        'id' => 'formCondutor',
        'options' => ['enctype'=>'multipart/form-data'],
        'encodeErrorSummary' => false,
        'errorSummaryCssClass' => 'help-block',
    ]); ?>
    <?= $form->field($model, 'novaSenha')->passwordInput(['maxlength' => true]); ?>

    <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>