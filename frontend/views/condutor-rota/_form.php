<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\CondutorRota;
use common\models\Condutor;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\CondutorRota */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>
    <?php if(!$model->isNewRecord): ?>
    <div class="row">
      <div class="col-md-12">
      <h4>Condutor(a): <?= $model->condutor->nome ?></h4>
      </div>
    </div>
    <?php endif; ?>
	
    <div class="row">
    <?php if($model->isNewRecord){ ?>
    	<div class="col-md-6">
    		    <?=  
                  $form->field($model, 'idCondutor')->widget(Select2::classname(), [
                     'data' => ArrayHelper::map(Condutor::disponivelRota($model->condutor), 'id', 'nome'),
                    'value' =>  $model->condutor,
                    'language' => 'pt',
                    'options' => ['placeholder' => 'Selecione o condutor', 'class' => 'form-control', 'id' => 'condutor'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'initialize' => true,
                    ],
                ]);
            ?>
    	</div>
	<?php }else{ ?>
		<div class="col-md-6"></div>
	<?php } ?>
        <div class="col-md-6">
                   <?=
                      $form->field($model, 'sentido')->widget(Select2::classname(), [
                        'data' => CondutorRota::ARRAY_SENTIDO,
                        'language' => 'pt',
                        'options' => ['placeholder' => 'Sentido', 'class' => 'form-control', 'id' => 'sentido'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => false,
                            'initialize' => true,
                        ],
                    ]);
                ?>
            </div>
    </div>
	
    <div class="row">
      <div class="col-md-6">
              <?=
                $form->field($model, 'turno')->widget(Select2::classname(), [
                  'data' => CondutorRota::ARRAY_TURNOS,
                  'language' => 'pt',
                  'options' => ['placeholder' => 'Turnos', 'class' => 'form-control', 'id' => 'turno'],
                  'pluginOptions' => [
                      'allowClear' => true,
                      'multiple' => false,
                      'initialize' => true,
                  ],
              ]);
          ?>
      </div>
      <div class="col-md-6">
              <?=
                $form->field($model, 'viagem')->widget(Select2::classname(), [
                  'data' => CondutorRota::ARRAY_VIAGEM,
                  'language' => 'pt',
                  'options' => ['placeholder' => 'Viagem', 'class' => 'form-control', 'id' => 'viagem'],
                  'pluginOptions' => [
                      'allowClear' => true,
                      'multiple' => false,
                      'initialize' => true,
                  ],
              ]);
          ?>
      </div>
    </div>
	
	<div class="row">
    	<!-- <div class="col-md-3">
    		   <?=
                  $form->field($model, 'turno')->widget(Select2::classname(), [
                    'data' => CondutorRota::ARRAY_TURNOS,
                    'language' => 'pt',
                    'options' => ['placeholder' => 'Sentido', 'class' => 'form-control', 'id' => 'turno'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'initialize' => true,
                    ],
                ]);
            ?>
    	</div> -->
    
    	<!-- <div class="col-md-3">
    		<?= $form->field($model, 'entrada')->input('time') ?>
    	</div>
    	<div class="col-md-3">
    		<?= $form->field($model, 'saida')->input('time') ?>
    	</div> -->
    </div>
    <div class="row">
         <div class="col-md-12">
          <?= $form->field($model, 'descricao')->textarea(['rows' => '6','placeholder' => '']) ?> 
        </div>
    </div>

    <div class="form-group">
    	<?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!-- validação de horários -->
<script type="text/javascript">
  let inicio = $("#condutorrota-entrada");
  let fim = $("#condutorrota-saida");

  function validarHorario(){
    if(inicio.val() && fim.val() && inicio.val() >= fim.val()){
        fim.val('');
        fim.focus();
    }
  }

  inicio.focusout(() => {
    validarHorario();
  });
  fim.focusout(() => {
    validarHorario();
  });
</script>