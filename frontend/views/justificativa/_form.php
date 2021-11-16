<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Justificativa */
/* @var $form yii\widgets\ActiveForm */
use common\models\Justificativa;
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
    	<div class="col-md-6">
    		<?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>		
    	</div>
    	<div class="col-md-6">
			 <label class="control-label" for="regiao">Classificação</label>
				    <?php echo Select2::widget([
				            'model' => $model,
				            'attribute' => 'classificacao',
				            'data' =>  Justificativa::ARRAY_CLASSIFICACAO,
				            'options' => [
				                'id' => 'id-classificacao',
				                'placeholder' => 'Selecione a classificação',
				                'multiple' => false,
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
