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

<div class="box-body">
<div class="form-group">
  <input type="button" id='apagarPendencias' class="btn btn-warning pull-right" name="Apagar Pendências" value="Apagar Pendências"> 				
 </div>
</div>


<script type="text/javascript">


$(document).on('click', '#apagarPendencias', function () {  
	Swal.fire({
		title: 'Deseja apagar as pendencias de todos os condutores?',
        text: "",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'SIM',
        cancelButtonText: 'NÃO'			
	}).then((result) => {
		if(result.value){
			$.ajax({	
			type: 'POST',
			url: 'index.php?r=configuracao/apagar',
			data:{
				data: 1,
			},
			}).done(function(data) {
				if(data == 1){		
					Swal.fire({
					title: 'Todas as pendências foram apagadas',
					text: "",
					icon: 'warning',
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ok',
				}).then((result) => {
					
				});	
				}						
			});
		}
	});
});


</script>