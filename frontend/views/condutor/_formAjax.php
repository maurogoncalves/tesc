<?php
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use yii\helpers\Html;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */
?>
<style type="text/css">
::ng-deep .pac-container {
  z-index: 100000;
}

</style>
<div class="box-body">

    <?php $form = ActiveForm::begin(
        [
            'action' => Url::to([$action, 'id' => $model->id]),
            'options' => [
                'id' => 'formAjax',
                'enctype' => 'multipart/form-data'
            ],
            'validateOnBlur' => false,
            'enableClientValidation' => true,
            'encodeErrorSummary' => false,
            'errorSummaryCssClass' => 'help-block',
          //  'enableAjaxValidation' => true,

        ]
    ); ?>
    <?= $form->field($model, 'idEmpresa')->hiddenInput(['maxlength' => true])->label(false); ?>

     <?php echo Yii::$app->controller->renderPartial('_inputs', ['form' => $form, 'model' => $model ]);  ?>

    <div class="form-group">

        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Editar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script>  
    $(document).on("submit", "#formAjax", function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var $yiiform = $(this);
        var formData = new FormData($(this)[0]);
        $.ajax({
                type: $yiiform.attr('method'),
                url: $yiiform.attr('action'),
                data: formData,
                processData: false,
                contentType: false
            }).done(function(data) {
                if (data.status) {
                    console.log(data)
                    console.log("RECARREGAR O INPUT");
                    $.pjax.reload({container:"#gridCondutores"});
                    // $("#lancamento-idplanoconta").html("");
                    // $("#lancamento-idplanoconta").append("<option value=''><option>");
                    // $.get('index.php?r=planoconta/index-ajax').done((result) => {
                    //     result.forEach((item, i) => {
                    //         $("#lancamento-idplanoconta").append('<OPTION value="' + item.id + '">' + item.nome + '</OPTION>');
                    //     })
                    // });
                    $("#modal").modal('hide');
                    return false;
                } else if (data.validation) {
                    console.log("2")
                    console.log(data)
                    if(data.validation.cpf){
                      Swal.fire(
                            'Este CPF já foi utilizado',
                            data.validation.cpf,
                            'error'
                        );
                    }   
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places

                } else {
                    console.log("3")
                    console.log(data)
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                    return false;
                }
            })
            .fail(function() {
            })

        return false;
    });
    // $.fn.capitalize = function() {
    //     var wordsToIgnore = ["to", "and", "the", "it", "or", "that", "this"],
    //         minLength = 3;

    //     function getWords(str) {
    //         return str.match(/\S+\s*/g);
    //     }
    //     this.each(function() {
    //         var words = getWords(this.value);
    //         $.each(words, function(i, word) {
    //             if (wordsToIgnore.indexOf($.trim(word)) == -1 && $.trim(word).length > minLength) {
    //                 words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
    //             }
    //         });
    //         this.value = words.join("");
    //     });
    // };

    // $('#planoconta-nome').on('keypress', function() {
    //     $(this).capitalize();
    // }).capitalize();.

</script>


<script>
var marker = false; 
var geocoder; 
var address;
var selectedDiv;
var currentLocation;
var search;
var enderecoAtual = '<?=   $model->endereco ?>';
var geocodeService = L.esri.Geocoding.geocodeService();

var map = L.map("map_canvas_ajax", {
    'center': [-23.223701,-45.9009074],
    'zoom' : 7,
  }).setView([-23.223701,-45.9009074], 7); 
  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

$(document).ready(function() {
  

  $("#condutor-tipocontrato").change(function() {
    if (this.value == 1) // Viagem
    {
      $("#minKmDia").hide();
      $("#maxKmDia").hide();
      $("#maxViagensDia").show();
    }
    else // Km
    {
      $("#minKmDia").show();
      $("#maxKmDia").show();
      $("#maxViagensDia").hide();
    }
  })
});
  
$("#saveLocation").on('click', function(event){
  $("#condutor-lat").val(marker.getLatLng().lat);
    $("#condutor-lng").val(marker.getLatLng().lng);
    $('#modal').modal("hide"); 
});
$(".pickLocation").on('click', function(event){
$('#modal').modal("show");
});

</script>