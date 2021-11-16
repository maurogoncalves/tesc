<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\AlunoRota */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin(
        [
            'action' => Url::to([$action, 'id' => $model->id, 'idCondutor' => $idCondutor]),
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

    <?= $form->field($model, 'idCondutorRota')->hiddenInput()->label(false) ?>

    <div class="row">
    	<div class="col-md-6">
                   <div class="form-group">
        <label class="control-label" for="idServico">Escola</label>
        <?php 
        echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'idEscola',
                     'data' => ArrayHelper::map($escolas, 'escola.id', 'escola.nome'),
                    'options' => [
                        'id' => 'id-unidade',
                        'placeholder' => 'Selecione a escola',
                        'multiple' => false,
                    ],
                    'pluginEvents' => [
                        "change" => 'function() { 
                             
                              $("#id-tipo").select2("val", "");
                              $("#id-tipo").html("");  

                              $.get( "index.php?r=aluno/aluno-escola-ajax", { idEscola: $(this).val() } )
                              .done(function( data ) {
                                console.log(data);
                                data.forEach((item, i) => {
                                    console.log(item);
                                      $("#id-tipo").append($("<option/>", {
                                        value: item.id,
                                        text: item.nome
                                    }));
                                });
                              });
                           
                        }',
                    ],
                ]);

            ?>
            </div>
    	</div>
    
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="idServico">Aluno</label>
                <?php 
                echo Select2::widget([
                            'model' => $model,
                            'attribute' => 'idAluno',
                            'data' => [],
                            'options' => [
                                'id' => 'id-tipo',
                                'placeholder' => 'Selecione o aluno',
                                'multiple' => false,
                            ],
                            'pluginEvents' => [
                                "change" => 'function() { 

                                }',
                            ],
                        ]); 
                ?>
            </div>
        </div>
    </div>



    <div class="form-group">
   	 <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
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
                    $.pjax.reload({container:"#grid"});
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
 

</script>