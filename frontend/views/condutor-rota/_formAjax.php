<?php
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use common\models\CondutorRota;

use yii\helpers\Html;
use kartik\select2\Select2;
use common\models\Escola;

/* @var $this yii\web\View */
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */
?>

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
        ]
    ); ?>
   
    <?= $form->field($model, 'idCondutor')->hiddenInput(['maxlength' => true])->label(false); ?>    
    <div class="row">
        <div class="col-md-3">
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
        </div>
        <div class="col-md-3">
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
        <div class="col-md-3">
            <?= $form->field($model, 'entrada')->input('time') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'saida')->input('time') ?>
        </div>
    </div>
    <div class="row">
         <div class="col-md-12">
          <?= $form->field($model, 'descricao')->textarea(['rows' => '6','placeholder' => '']) ?> 
        </div>
    </div>

   



    <div class="form-group">
    <br>
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Editar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    // $( ".checkbox-vinculo" ).change(function() {
    //     alert($(this).val());
    // });
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
                    $.pjax.reload({container:"#gridVinculo"});
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
    // }).capitalize();
</script>


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