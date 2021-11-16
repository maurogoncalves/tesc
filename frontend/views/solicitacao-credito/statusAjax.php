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
/* @var $this yii\web\View */
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */



?>

<div class="box-body">

    <?php $form = ActiveForm::begin(
        [
            'action' => Url::to([$action, 'id' => $solicitacao->id, 'status' => $status]),
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
   

    <?= $form->field($model, 'idSolicitacaoCredito')->hiddenInput(['maxlength' => true])->label(false); ?> 

    
    <div class="row" id="justificativa">
         <div class="col-md-12">
         <?php 
        
                echo $form->field($model, 'justificativa')->textarea(['rows' => '6','placeholder' => '']);
          
        ?> 
        </div>
    </div>
    <div class="form-group">
    <br>
        <?= Html::submitButton( 'Salvar', ['class' => 'btn btn-success pull-right' , 'id' => 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

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
                    location.reload();
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