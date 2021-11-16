<?php

use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use yii\helpers\Html;
use common\models\SolicitacaoTransporte;
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
            // 'validateOnBlur' => false,
            // 'enableClientValidation' => false,
            // 'encodeErrorSummary' => false,
            // 'errorSummaryCssClass' => 'help-block',
            // 'validateOnBlur' => false,
            // 'enableClientValidation' => false, 
            'validateOnBlur' => false,
            'enableClientValidation' => true,
        ]
    ); ?>

    <?php
    if ($model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO) {
        echo Yii::$app->controller->renderPartial('_inputs', ['form' => $form, 'model' => $model, 'escolas' => $escolas]);
    } else {
        echo Yii::$app->controller->renderPartial('_inputsCancelamento', ['form' => $form, 'model' => $model]);
    }
    ?>

    <div class="form-group">
        <br>
        <?= Html::submitButton($model->isNewRecord ? $label : 'Editar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right', 'id' => 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script>
    $("#submitButton").prop("disabled", true);
    window.request = false;
    $(document).ready(function () {
    $('form').on('beforeSubmit', function() {
    if (window.request == true) return false;
    window.request = true;
    var $form = $(this);
    var $submit = $form.find(':submit');
        $submit.html('<span class="fa fa-spin fa-spinner"></span> Processando...');
        $submit.prop('disabled', true);
    });
    })
    $(document).off("submit", "#formAjax");
    $(document).on("submit", "#formAjax", function(e) {
        console.log('SUBMIT');

        e.preventDefault();
        e.stopImmediatePropagation();
        // if ($("#solicitacaotransporte-tiposolicitacao").val() == 1 && !$("#formalizacaoSol").val()) {
        //     addError('.field-formalizacaoSol');
        //     Swal.fire(
        //         'Atenção!',
        //         'Anexe a formalização da solicitação preenchida.',
        //         'warning'
        //     )
        //     return false;
        // } else {
        //     delError('.field-formalizacaoSol');

        // }
        console.log("submit")
        console.log($("#solicitacaotransporte-tiposolicitacao").val())
        if ($("#solicitacaotransporte-tiposolicitacao").val() == 2 && !$("#solicitacaotransporte-justificativabarreirafisica").val()) {
            addError('.field-solicitacaotransporte-justificativabarreirafisica');
            Swal.fire(
                'Atenção!',
                'O campo justificativa é obrigatório.',
                'warning'
            )
            return false;
        }

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
                    $.pjax.reload({
                        container: "#gridSolicitacoes"
                    });
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
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places

                    console.log("2")
                    console.log(data)
                } else {
                    console.log("3")
                    console.log(data)
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                    return false;
                }
            })
            .fail(function() {})

        return false;
    });
</script>