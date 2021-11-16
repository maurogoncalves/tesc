<?php

use common\models\Aluno;
use common\models\ReciboPagamentoAutonomo;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use common\models\SolicitacaoCredito;
use common\models\Usuario;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoCreditoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitação: ' . $model->id;
$this->params['breadcrumbs'][] = $this->title;
function td($str)
{
    return '<td>' . $str . '</td>';
}
?>

<?= $this->render('dados-iniciais', ['model' => $model, 'mostrarExportacao' => true, 'mostrarAprovacao' => true, 'configuracao' => $configuracao]) ?>


<div class="row mt-10">
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Dias letivos do mês</label>
            <input type="text" readonly="true" class="form-control" id="diasLetivosMes"  value="<?= $model->diasLetivosMes ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Valor Necessário</label>
            <input name="valorNecessarioTotal" id="valorNecessarioTotal"  readonly="true" class="form-control moneyWithMinus" value="<?= $model->valorNecessarioTotal ? $model->valorNecessarioTotal : 0;?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Saldo Restante na Escola</label>
            <input name="saldoRestanteEscola" id="saldoRestanteEscola" readonly="true" class="form-control moneyWithMinus" value="<?= $model->saldoRestante ? $model->saldoRestante : 0; ?>" class="form-control mt-15 moneyWithMinus">
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Dias Letivos Restantes</label>
          
            <input name="diasLetivosRestantes"  readonly="true" class="form-control" value="<?= $model->diasLetivosRestantes ?>" class="form-control mt-15 ">

        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Saldo Restante nos Cartões</label>
            <input type="text" name="saldoRestanteCartoes" id="saldoRestanteCartoes" readonly="true" class="form-control mt-15 moneyWithMinus"  value="<?= $model->saldoRestanteCartoes ? $model->saldoRestanteCartoes : 0; ?>" >
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Valor a ser Creditado</label>
            <input type="text" name="valorCreditado" id="valorCreditado" readonly="true" class="form-control mt-15 moneyWithMinus"  value="<?= $model->valorCreditado ? $model->valorCreditado : 0; ?>" >
        </div>
    </div>

</div>

<div class="row mt-10">
    <div class="col-md-8">
        <div class="box box-solid" >
            <h3><?=  $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? 'Passe Escolar' : 'Vale Transporte' ?></h3>
            <div class="pull-right" style="display: inline-flex;    align-items: center;">
                <form class="form-inline">
                    <b>Alunos atendidos:</b> 
                    <input type="text" class="form-control qtdeAlunos" id="qtdeAlunos" style="max-width: 50px;padding-right:15px;" readonly="true" value="<?= count($solicitacoesAlunos) ?>" />
                </form>
            </div>
            
            <table class="table table-responsive table-bordered">
                <tr>
                    <th >--</th>
                    <th style="display: none;">Benefício</th>
                    <?= $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? '<th>FUNDHAS</th>': ''?>
                    <th>Nome</th>
                    <th>RA</th>
                    <th>Turma</th>
                    <th>Nº Cartão</th>
                    <th>Saldo em <?= date("d/m/Y", strtotime($model->criado)) ?></th>
                    <th>Justificativa </th>
                    <th>Valor Necessário </th>
                    <th>Necessidade de crédito</th>
                    <th>&nbsp;</th>
                </tr>
                <?php $i = 0;
                foreach ($solicitacoesAlunos as $solAluno) :  $i++; ?>
                <tr>
                    <td class="center-td"><?= $i ?></td>
                    <td class="center-td" style="display: none;"><input type="checkbox" class="alunoMarcado" checked="true" name="aluno[]" value="<?= $solAluno->aluno->id ?>" /></td>
                    <?php if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR): ?>
                    <td class="center-td"><input type="checkbox" class="fundhas" <?= $solAluno->fundhas == 'on' ? 'checked=""' : ''?>  onclick="return false;" /></td>
                    <?php endif; ?>
                    <!-- <td style="text-align: center; vertical-align: middle;"><input type="checkbox" /></td> -->
                    <td class="center-text"><a target="_new"
                            href="<?= Url::toRoute(['aluno/view', 'id' =>  $solAluno->aluno->id]) ?>"><?= $solAluno->aluno->nome ?></a></td>
                    <td class="center-text"><?= $solAluno->aluno->RACompleto ?></td>
                    <td class="center-text"><?= $solAluno->aluno->turma ? Aluno::ARRAY_SERIES[$solAluno->aluno->serie].'/'.Aluno::ARRAY_TURMA[$solAluno->aluno->turma] : '-' ?></td>
                    <td class="center-text"><?= $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? $solAluno->aluno->solicitacaoAtivaPasse->cartaoPasseEscolar : $solAluno->aluno->solicitacaoAtivaPasse->cartaoValeTransporte ?></td>
                    <td class="center-text">
                        <input type="text" class="form-control moneyWithMinus inputSaldoRestante"   value="<?= $solAluno->saldo ? $model->saldo : 0; ?>" readonly="true">
                    </td>
                    <td style="background:red; display:none;">
                        <input type="text" class="form-control inputDiasLetivosFecharMes" readonly="true">
                    </td>
                    <td style="background:red; display:none;">
                        <input type="text" class="form-control inputAntiUe" readonly="true">
                    </td>
                    <td style="background:red; display:none;">
                        <input type="text" class="form-control saldoFinalMes" readonly="true">
                    </td>
                    <td class="center-text"><input type="text" class="form-control justificativa"  value="<?= $solAluno->justificativa ?>" readonly="true"></td>
                    <td class="center-text">
                        <div class="">
                        <input type="text" class="form-control valorNecessario moneyWithMinus"  readonly="true"  value="<?= $solAluno->valor ? $model->valor : 0; ?>" >
                        </div>
                    </td>
                    <td class="center-td" style="max-width: 100px !important;">
                        <div class="">
                            <div class="simple-toggle">
                                <label class="tgl">
                                    <input type="checkbox" class="a checkboxNecessidadeCredito"   />
                                    <span data-on="Sim" data-off="Não"></span>
                                </label>
                            </div>
                        </div>
                        <div class="avisoNecessidadeCredito"  style="max-width: 100px !important;color:#D8544F">
                            <!-- <i class="fas fa-exclamation-triangle"></i> Necessário desmarcar -->
                        </div>
                    </td>

                    <td class="center-td"><a class="btn btn-primary pull-right consultarCredito"
                            aluno="<?= $solAluno->id ?>"><i class="fas fa-search"></i></a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div class="pull-right">
                <form class="form-inline">
                    <b>Alunos atendidos:</b> <input type="text" class="form-control qtdeAlunos"
                        style="max-width: 45px;padding-right:15px;" readonly="true" value="<?= count($solicitacoesAlunos) ?>" />
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-solid mt-15">
            <div class="box-header with-border">
                <h3>Histórico de status</h3>
            </div>
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->historico,
                        'key' => 'id',
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                    ]),
                    'pjax' => true,
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        'options' => [
                            'id' => 'grid',
                        ]
                    ],
                    'options' => [
                        'class' => 'table-header-ajax',
                    ],
                    'summary' => '',
                    'striped' => false,
                    'bootstrap' => true,
                    'emptyText' => '<h3 class="vazio">Nenhum status</h3>',
                    'columns' => [
                        [
                            'attribute' => 'dataCadastro',
                            'label' => 'Data',
                            'value' => function ($model, $index, $widget) {
                                return ($model->dataCadastro) ? Yii::$app->formatter->asDate($model->dataCadastro, 'dd/MM/Y') : '';
                            },
                            'filterType' => GridView::FILTER_DATE,
                            'filterWidgetOptions' => [
                                'pluginOptions' => [
                                    'format' => 'dd/mm/yyyy',
                                    'autoclose' => true,
                                    'todayHighlight' => true,
                                ]
                            ]
                        ],
                        [
                            'attribute' => 'idUsuario',
                            'label' => 'Usuário',
                            'value' =>  function ($model) {
                                return $model->usuario->nome;
                            },
                        ],
                        'justificativa',
                        [
                            'attribute' => 'status',
                            'label' => 'Status',
                            'filter' => false,
                            'value' => function ($model) {
                                return $model->status ?  SolicitacaoCredito::ARRAY_STATUS[$model->status] : '-';
                            }
                        ],


                    ],
                ]); ?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
    <div class="" style="margin-top:20px;">
                                                                                            
</div>



<div id="modal2" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Informações do aluno</h4>
            </div>
            <div class="modal-body" id="informacoes">
                <div class="row">
                    <div class="col-md-12">
                        <label>CPF</label>
                        <div class="input-group">
                            <input type="text" id="cpf" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Cartão de Vale Transporte</label>
                        <div class="input-group">
                            <input type="text" id="cartaoValeTransporte" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Cartão de Passe Escolar</label>
                        <div class="input-group">
                            <input type="text" id="cartaoPasseEscolar" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">

$(document).ready(function() {
    $.getJSON("index.php?r=configuracao%2Fview-ajax")
.done(function(response) {
    if(tipo == 'passeEscolar') {
        valorAtualPasse = parseFloat(response.passeEscolar) * 2
    } else {
        valorAtualPasse = parseFloat(response.valeTransporte) * 2
    }
    configuracoes = response; 
});
    $('.moneyWithMinus').each(function() {
        $(this).val(realToBRL(parseFloat($(this).val())));
    })


    $(".a").click(function(event)  {
        event.preventDefault(); 
    })
    $('[data-toggle="tooltip"]').tooltip();

    $('.copy').on("click", function() {
        let el = $(this).parent().parent().find('input');
        el.select();
        document.execCommand('copy');
    });

    $(".consultarCredito").click(function() {
        // console.log(this);
        let idAluno = $(this).attr('aluno');
        $.get('index.php?r=aluno/aluno-ajax', {
            id: idAluno
        }).done((result) => {
            // console.log(result);
            $('#modal2').modal("show");

            $("#cpf").val(result.cpf)
        });

        $.get('index.php?r=solicitacao-transporte/view-solicitacao-ajax', {
            id: idAluno
        }).done((result) => {
            // console.log(result);
            $('#modal').modal("show");
            $("#cartaoPasseEscolar").val(result.cartaoPasseEscolar)
            $("#cartaoValeTransporte").val(result.cartaoValeTransporte)
        });
    });
    marcarChecks();
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

function marcarChecks(){
       $(".checkboxNecessidadeCredito").each(function() {
        //
         let valor = BRLtoReal($(this).closest('tr').find('.valorNecessario').val())
         console.log(valor);
         if(valor > 0) {
             $(this).prop('checked', true) 
         } else {
             $(this).prop('checked', false) 
         }
    });
}
 function realToBRL(numero){
    var numero = numero.toFixed(2).split('.');
    numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
    return numero.join(',');
}
</script>