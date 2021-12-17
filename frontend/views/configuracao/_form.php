<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Configuracao */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>
			  
	<?=  $form->field($model, 'exibeRenovacao')
            ->dropDownList(
            [ '1' => 'Sim','0' => 'Não',],          
			['prompt'=>'Selecione']    
            );
	?>
 
    <?= $form->field($model, 'valeTransporte')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'passeEscolar')->textInput(['maxlength' => true]) ?>

    <?php 
        echo $form->field($model, 'dataVigente')->widget(DatePicker::classname(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'value' =>  $model->dataVigente,
            'options' => ['placeholder' => 'Data'],
            'pluginOptions' => [
                'orientation' => 'bottom left',
                'autoclose'=>true,
                'format' => 'dd/mm/yyyy',
                // 'startDate' => 'today',
            ]
        ]);
    ?>
	<?php 
        echo $form->field($model, 'anoVigente')->widget(DatePicker::classname(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'value' =>  $model->anoVigente,
            'options' => ['placeholder' => 'Ano Vigente para novas solicitações'],
            'pluginOptions' => [
                'orientation' => 'bottom left',
                'autoclose'=>true,
                'format' => 'yyyy',
                // 'startDate' => 'today',
            ]
        ]);
    ?>
      <div class="template-fileinput <?php if (empty($model->folhaPonto)) print 'without-files'; ?>">

        <?php
        echo $form->field($model, 'documentoFolhaPonto')->widget(FileInput::classname(), [ 
          'options' => ['accept' => 'application/pdf', 'multiple' => false, 'id' => 'aluno-rgresponsavel'],
          'pluginOptions' => ['allowedFileExtensions' => ['pdf'], 'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
        ])->label('Folha de ponto'); ?>
        <div class="substituir-arquivos">Clique aqui para substituir o arquivo</div>
    
    </div>
	
	 
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
