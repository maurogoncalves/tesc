<?php

use yii\helpers\Html;

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use common\models\Escola;
use common\models\ReciboPagamentoAutonomo;
use common\models\SolicitacaoCredito;

/* @var $this yii\web\View */
/* @var $model common\models\SolicitacaoCredito */
switch($model->tipoSolicitacao){
    case SolicitacaoCredito::TIPO_PASSE_ESCOLAR: $tipo = 'Passe Escolar'; break;
    case SolicitacaoCredito::TIPO_VALE_TRANSPORTE: $tipo = 'Vale Transporte'; break;
    case SolicitacaoCredito::TIPO_CREDITO_ADMINISTRATIVO: $tipo = 'Crédito Administrativo'; break;
    default: $tipo = ''; break;
}
$this->title = 'Nova solicitação de crédito - '.$tipo;
$this->params['breadcrumbs'][] = ['label' => 'Solicitação de crédito', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
		    <div class="box-header with-border">
		    	<h3><?= Html::encode($this->title) ?></h3>
		    </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                        'id' => 'criarCredito',
                        'options' => ['enctype' => 'multipart/form-data'],
                        'encodeErrorSummary' => false,
                        'errorSummaryCssClass' => 'help-block',
                ]); ?> 
                <div class="col-md-6"> 
                    <?=
                        $form->field($model, 'idEscola')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(Escola::escolasPerfis($model->escola), 'id', 'nomeCompleto'),
                            'value' =>  $model->escola,
                            'language' => 'pt',
                            'options' => ['placeholder' => 'Selecione a escola', 'class' => 'form-control', 'id' => 'escola'],
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
                $form->field($model, 'mesInicio')->widget(Select2::classname(), [
                    'data' => ReciboPagamentoAutonomo::ARRAY_MESES,
                    'language' => 'pt',
                    'options' => ['placeholder' => 'Início', 'class' => 'form-control', 'id' => 'inicio'],
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
                    $form->field($model, 'mesFim')->widget(Select2::classname(), [
                        'data' => ReciboPagamentoAutonomo::ARRAY_MESES,
                        'language' => 'pt',
                        'options' => ['placeholder' => 'Fim', 'class' => 'form-control', 'id' => 'fim'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => false,
                            'initialize' => true,
                        ],
                    ]); 
                   ?>
                </div>
                <?php if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_CREDITO_ADMINISTRATIVO): ?>
                <div class="col-md-6">
                    <?= $form->field($model, 'numeroCartaoAdministrativo')->textInput(['id' => 'numeroCartaoAdministrativo','min' =>"0","max" =>" 9999999999",'placeholder' => 'Número do cartão administrativo', 'type' => 'number']); ?>
                </div> 
                <div class="col-md-6">
                    <?= $form->field($model, 'creditoAdministrativo')->textInput(['class' => 'form-control money', 'id' => 'valorTotal']); ?>
                </div> 
                <?php endif; ?>

                <div class="col-md-12">
                    <div class="form-group">
                     <a class="btn btn-success pull-right" id="salvar" >Salvar</a>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
		</div>
	</div>
</div>
<script>
var tipo = <?= $model->tipoSolicitacao; ?>;
// tipoSolicitacao = TIPO_CREDITO_ADMINISTRATIVO = 3
$("#salvar").click(function(ev) { 
    event.preventDefault(); 
    let escola = $("#escola").val();
    let inicio = parseInt($("#inicio").val());
    let fim = parseInt($("#fim").val());
    let numeroCartaoAdministrativo = parseInt($("#numeroCartaoAdministrativo").val());
    let valorTotal = $("#valorTotal").val();
    console.log(escola,inicio,fim)
    if(!numeroCartaoAdministrativo && tipo == 3)
        return aviso('Preencha todos os campos')
    if(!valorTotal && tipo == 3)
        return aviso('Preencha todos os campos')
    
    if(!escola || !inicio || !fim)
        return aviso('Preencha todos os campos')
    if(inicio > fim)
        return aviso('Início não pode ser maior que Fim')
    if(fim < inicio)
        return aviso('Fim não deve ser menor que Início')
    return $( "#criarCredito" ).submit();
});

function aviso(aviso){
    return Swal.fire( 'Atenção!', aviso,'warning')
}
$(document).ready(function() {
        $("#valorTotal").keyup(function(){
            let valor = BRLtoReal($(this).val());
            if( valor > 100) {
                $(this).val("")
                return Swal.fire( 'Atenção!', 'O valor deve ser menor que 100','warning')
            }
        })
        $("#valorTotal").change(function(){
            let valor = BRLtoReal($(this).val());
            if(valor < 30 || valor > 100) {
                $(this).val("")
                return Swal.fire( 'Atenção!', 'O valor deve ser entre 30 e 100','warning')
            }
        })
        $("#valorTotal").inputFilter(function(value) {
            return /^-?\d*[.,]?\d*$/.test(value)
        });
});
function BRLtoReal(valor){
    if(valor === ""){
        valor =  0;
    }else{
        valor = valor.replace(".","");
        valor = valor.replace(",",".");
        valor = parseFloat(valor);
    }
    return valor;
}


</script>
