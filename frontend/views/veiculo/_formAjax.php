<?php
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Veiculo;
use yii\helpers\Html;

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
          //  'enableAjaxValidation' => true,

        ]
    ); ?>
    <?= $form->field($model, 'idProprietarioEmpresa')->hiddenInput(['maxlength' => true])->label(false); ?>
    <?= $form->field($model, 'tipoProprietario')->hiddenInput(['maxlength' => true])->label(false); ?>


   <?php echo Yii::$app->controller->renderPartial('_inputs', ['form' => $form, 'model' => $model ]);  ?>


    <div class="form-group">
        <br>
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
                    //$.pjax.reload({container:"#gridVeiculos"});
                    $.pjax.reload('#gridVeiculos')
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
                    console.log(JSON.stringify(data.validation));
                    
                        // for(var campo in data.validation) {
                        //     console.log(tipoNome, data.validation[campo]);
                        // }
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places

                } else {
                    console.log("3")
                    console.log(data)
                    console.log(data.validation);
                    console.log(JSON.stringify(data));
                    //Não funciona com validações de unique no model, então fiz isso
                    if(data['veiculo-placa'])
                        Swal.fire(
                            'Esta placa já foi cadastrada',
                            'Realize o cadastro de uma placa diferente',
                            'error'
                        );
                    try {
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                    } catch(e){
                        console.log(e);
                    }
                    return false;
                }
            })
            .fail(function() {
            })

        return false;
    });

</script>


