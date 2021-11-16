<?php
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use yii\helpers\Html;
use common\models\Usuario;
use common\models\SolicitacaoTransporte;
use common\models\CalendarioDia;
use kartik\select2\Select2;

/* @var $this yii\web\View */ 
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */



?>

<div class="box-body">

    <?php $form = ActiveForm::begin(
        [
            //'id' => $solicitacao->id, 'status' => $status
            'action' => Url::to([$action]),
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
    <div class="alert alert-light" role="alert">
      Atenção: A criação de um novo registro não altera solicitações de crédito passadas.
    </div>

    <?= $form->field($model, 'idCalendario')->hiddenInput(['maxlength' => true])->label(false); ?> 

 
    <div class="col-md-6">
        <?php 
            echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'value' =>  $model->data,
                'options' => ['placeholder' => 'Data', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'orientation' => 'bottom left',
                    'autoclose'=>true,
                    'todayhighlight' => true,
                    'format' => 'dd/mm/yyyy',
                    // 'startDate' => 'today',
                ]
            ]);
        ?>
    </div>
    <div class="col-md-6">
   
         <?= $form->field($model, 'tipo')->dropDownList(CalendarioDia::ARRAY_TIPO,['prompt' => 'SELECIONE'] ) ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'descricao')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
    </div>

    <div class="form-group">
    <br>
        <?= Html::submitButton( 'Salvar', ['class' => 'btn btn-success pull-right' , 'id' => 'submitButton']) ?>
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
                    //$.pjax.reload({container:"#gridSolicitacoes"});
                    // $("#lancamento-idplanoconta").html("");
                    // $("#lancamento-idplanoconta").append("<option value=''><option>");
                    // $.get('index.php?r=planoconta/index-ajax').done((result) => {
                    //     result.forEach((item, i) => {
                    //         $("#lancamento-idplanoconta").append('<OPTION value="' + item.id + '">' + item.nome + '</OPTION>');
                    //     })
                    // });
                    $("#modal").modal('hide');
                    $("#w1").fullCalendar('refetchEvents');
                    return false;
                } else if (data.validation) {
                    if(data.validation.data)
                        Swal.fire(
                            'Esta data já foi atribuída',
                            'Selecione outra data para continuar',
                            'error'
                        );
                    console.log("2")
                    console.log(data)
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places

                } else {
                    if(data.validation.data)
                        Swal.fire(
                            'Esta data já foi atribuída',
                            'Selecione outra data para continuar',
                            'error'
                        );
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