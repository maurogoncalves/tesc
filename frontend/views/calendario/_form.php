<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Escola;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Calendario */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>
    
    <div class="row">
        <div class="col-md-12">
            
            <div class="form-group">
             
             <?php 
              echo '<label class="control-label">Tipo</label>';
              echo Select2::widget([
                  'name' => 'inputEscola',
                  'data' => Escola::disponiveisCalendario(),
                  'options' => [
                    'id' => 'tipo-select',  
                    'prompt' => 'SELECIONE', 
                    'placeholder' => '',
                    'multiple' => true
                  ],
                  'pluginOptions' => [
                    'allowClear' => true
                  ], 
                  'pluginEvents' => [
                    "change" => 'function() { 
                        habilitarSubmit($(this).val())
                    }',
                  ]
              ]);
       

            
                ?>
            </div>
        </div>
        <div class="col-md-12">
      
        </div>
    </div>
    <div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Editar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right', 'id' => 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>    
   $("#submitButton").prop("disabled",true);
   function habilitarSubmit(value){
       console.log(value);;
       if(value.length){
        $("#submitButton").prop("disabled",false);
       } else {
        $("#submitButton").prop("disabled",true);
       }
   }
 
</script>
