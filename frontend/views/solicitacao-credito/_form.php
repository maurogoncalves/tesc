<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\widgets\FileInput;
use common\models\Escola;
use common\models\SolicitacaoCredito;
/* @var $this yii\web\View */
/* @var $model common\models\SolicitacaoCredito */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?> 
    <?= 
        $form->field($model, 'status')->hiddenInput(['value'=> SolicitacaoCredito::STATUS_EM_ANDAMENTO])->label(false);
    ?>
    
    <div class="row">   
           <div class="col-md-6">
        <?=
              $form->field($model, 'idEscola')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Escola::escolasPerfis($model->escola), 'id', 'nome'),
                'value' =>  $model->escola,
                'language' => 'pt',
                'options' => ['placeholder' => 'Selecione a escola', 'class' => 'form-control', 'id' => 'escola-'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'initialize' => true,
                ],
            ]);
        ?>
    </div>
      <div class="col-md-6">

             <?php 
             echo '<label class="control-label">Período</label>';
            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'inicio',
                'attribute2' => 'fim',
                'options' => ['placeholder' => 'Início ', 'autocomplete' => 'off'],
                'options2' => ['placeholder' => 'Fim ', 'autocomplete' => 'off'],
                'type' => DatePicker::TYPE_RANGE,
                'form' => $form,
                      'separator' => 'até',
                    'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                    'orientation' => 'bottom left',
                     'format' => 'dd/mm/yyyy',
                ]
            ]);             
            ?>
    </div>

    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
