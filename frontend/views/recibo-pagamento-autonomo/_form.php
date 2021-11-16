<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use common\models\Escola;
use kartik\date\DatePicker;
use common\models\Condutor;
use common\models\ReciboPagamentoAutonomo;
use kartik\widgets\FileInput;
use common\models\NecessidadesEspeciais;

/* @var $this yii\web\View */
/* @var $model common\models\ReciboPagamentoAutonomo */
/* @var $form yii\widgets\ActiveForm */

$arrayAnos = [];
for ($i = intval(date('Y')) - 1; $i <= intval(date('Y')); $i++)
    $arrayAnos[$i] = $i;
?>
<script type="text/javascript">
    var condutor = null;
</script>
<div class="box-body">

    <?php $form = ActiveForm::begin([
        'id' => 'formRecibo',
        'options' => ['enctype' => 'multipart/form-data'],
        'encodeErrorSummary' => false,
        'errorSummaryCssClass' => 'help-block',

    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <?=
                $form->field($model, 'idCondutor')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'),
                    'value' =>  $model->idCondutor,
                    'language' => 'pt',
                    'options' => ['placeholder' => 'Selecione o condutor', 'class' => 'form-control', 'id' => 'condutor'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'initialize' => true,
                    ],
                    'pluginEvents' => [
                        "change" => 'function() { 
                              $.get( "index.php?r=condutor/condutor-json", { id: $(this).val() } )
                              .done(function( data ) {
                                console.log(data);
                                condutor = data;

                                if(data.tipoContratoText == "" || data.valorPagoKmViagemText == "")
                                {
                                    console.log("hide")
                                    console.log(data.tipoContratoText)
                                    $("#parametros").hide();
                                    $("#alert").show();
                                }
                                else
                                {
                                    console.log("show")
                                    console.log(data.tipoContratoText)
                                    $("#parametros").show();
                                    $("#alert").hide();
                                }

                                if(data.tipoContratoText)
                                {
                                    $("#contrato").html(data.tipoContratoText);
                                    switch(data.tipoContrato)
                                    {
                                        case "1":
                                            $("[for=quantidade]").text("Quant. de viagens")
                                        break;

                                        case "2":
                                            $("[for=quantidade]").text("Quant. de Km")
                                        break;
                                    }

                                }
                                else 
                                    $("#contrato").html("-");

                                if(data.valorPagoKmViagemText)
                                    $("#valor").html(data.valorPagoKmViagemText);
                                else 
                                    $("#valor").html("-");

                                calcular();
                            });
                        }',
                    ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <b>Tipo do contrato:</b> <span id="contrato"></span><br>
            <b>Valor Base:</b> R$ <span id="valor"></span>
        </div>
    </div>

    <div class="row" id="alert">
        <div class="col-md-12">
            <div class="alert alert-light alert-dismissible">
                <h4><i class="icon fa fa-light"></i> Atenção</h4>
                Os parâmetros necessários para o cálculo do recibo não estão cadastrados.
            </div>
        </div>
    </div>

    <div class="row" id="parametros">
        <div class="col-md-2">
            <label class="control-label" for="mes">Mês</label>
            <?php echo Select2::widget([
                'model' => $model,
                'attribute' => 'mes',
                'data' =>  ReciboPagamentoAutonomo::ARRAY_MESES,
                'options' => [
                    'id' => 'id-mes',
                    'placeholder' => 'Mês',
                    'multiple' => false,
                ]
            ]);
            ?>
        </div>

        <div class="col-md-2">
            <label class="control-label" for="ano">Ano</label>
            <?php echo Select2::widget([
                'model' => $model,
                'attribute' => 'ano',
                'data' =>  $arrayAnos,
                'options' => [
                    'id' => 'id-ano',
                    'placeholder' => 'Ano',
                    'multiple' => false,
                ]
            ]);
            ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'quantidade')->textInput(['maxlength' => true, 'id' => 'quantidade', 'type' => 'number']); ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'diasLetivos')->textInput(['maxlength' => true, 'id' => 'diasLetivos', 'type' => 'number']); ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'valor')->textInput(['maxlength' => true, 'class' => 'form-control', 'id' => 'valorTotal']); ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'data')->widget(DatePicker::classname(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'value' =>  $model->data,
                'options' => ['placeholder' => 'Data'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy',
                    'startDate' => '-01y',

                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?php
            //   echo $form->field($model, 'documentoRecibo[]')->widget(FileInput::classname(), [
            // 'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            // 'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'showPreview' => false, 'initialPreview' => $model->docRecibo, 'showUpload' => false]
            // ])->label('Documento');

            ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script type="text/javascript">
    $("#parametros").hide();
    $("#alert").hide();
    $(document).ready(function() {
        $("#diasLetivos").change(() => {
            calcular();
        })
    });

    function numberToReal(numero) {
        var numero = numero.toFixed(2).split('.');
        numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
        return numero.join(',');
    }

    function calcular() {
        var quantidade = parseInt($("#quantidade").val());
        var diasLetivos = parseInt($("#diasLetivos").val());
        if (quantidade > 0 && condutor && condutor.valorPagoKmViagem) {
            let resultado = condutor.valorPagoKmViagem * quantidade * diasLetivos;

            $("#valorTotal").val(numberToReal(resultado));
        }
    }

    $(document).ready(function() {
        $("#valorTotal").inputFilter(function(value) {
            return /^-?\d*[.,]?\d*$/.test(value)
        });
    });
</script>