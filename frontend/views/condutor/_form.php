<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use common\models\Condutor;

use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Condutor */
/* @var $form yii\widgets\ActiveForm */
?>
 
<div class="box-body"> 
    <?php $form = ActiveForm::begin([
        'id' => 'formCondutor',
        'options' => ['enctype'=>'multipart/form-data'],
        'encodeErrorSummary' => false,
        'errorSummaryCssClass' => 'help-block',
    ]); ?>
    <?= $form->field($model, 'idEmpresa')->hiddenInput(['maxlength' => true])->label(false); ?>
    
    <?php echo Yii::$app->controller->renderPartial('_inputs', ['form' => $form, 'model' => $model ]);  ?>

    
    <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
  <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Confirmar o local</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div  id="location-map">
              <div id= "mapInput" style="min-height:500px;"></div>
              </div>
            </div>
          </div> 
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success pull-right" id="saveLocation">Confirmar</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">
    /*
    function seedForm(){
        let form = serializeToJson('#formCondutor');
        form.forEach((input) => {
            $( "input[name='"+input.name+"']" ).val('1');
        })
    }
    function serializeToJson(form) {
      if(!form.startsWith("#"))
        form = '#'+form;
      return JSON.parse(JSON.stringify( $(form).serializeArray() ) );
    }

    seedForm();
    */
</script>




<script type="text/javascript">
carregarTipoContrato(<?= $model->tipoContrato ?>);

$(document).ready(function() {

  
  $("#condutor-tipocontrato").change(function() {
    carregarTipoContrato(this.value);
  })

});
function carregarTipoContrato(tipo){
  $("#maxViagensDia").hide();
  $("#minKmDia").hide();
  $("#maxKmDia").hide();

  if (tipo == 1) // Viagem
    {
      $("#minKmDia").hide();
      $("#maxKmDia").hide();
      $("#maxViagensDia").show();
    }
    else if(tipo == 2) // Km
    {
      $("#minKmDia").show();
      $("#maxKmDia").show();
      $("#maxViagensDia").hide();
    }
}
$("#saveLocation").on('click', function(event){
  $("#condutor-lat").val(marker.getLatLng().lat);
    $("#condutor-lng").val(marker.getLatLng().lng);
    $('#modal').modal("hide"); 
});
$(".pickLocation").on('click', function(event){
$('#modal').modal("show");
});


</script>