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
        <?php 
        $lista = [];
        foreach ($model->bairrosPorZona($model->agrupamento) as $br) {
            array_push($lista, $br->idBairro);
          } 
          $model->bairrosDisponiveis = $lista;

        if($model->isNewRecord){
            $bairros =  ArrayHelper::map($bairrosDisponiveis, 'ID_BAIRRO', 'BAIRRO');
        } else {
            $bairros =  ArrayHelper::map($bairrosPorZona, 'idBairro', 'nome');
            foreach($bairrosDisponiveis as $bairro) {
                $bairros[$bairro->ID_BAIRRO] = $bairro->BAIRRO; 
            }
        }
              
        ?>
            <?= 
            
            $form->field($model, 'bairrosDisponiveis')->widget(Select2::classname(), [
            'data' => $bairros,
            'value' => '',
            'language' => 'pt',
            'options' => ['placeholder' => 'Selecione os bairros', 'class' => 'form-control', 'id' => 'necessidades'],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => true,
                'initialize' => true,
            ],
            ])
            ?>
        </div>
        <div class="col-md-4">
            <?=
              $form->field($model, 'agrupamento')
              ->dropDownList(
                  AgrupamentoBairro::ARRAY_BAIRRO,          
                  ['prompt'=>'Selecione', 'disabled' => !$model->isNewRecord]    
              );

        ?>
       </div>
    </div>

  <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
