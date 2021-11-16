<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use common\models\SolicitacaoCredito;
use common\models\Usuario;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use kartik\date\DatePicker;
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
<style type="text/css">
    input[type=checkbox] {
        /* Double-sized Checkboxes */
        -ms-transform: scale(2);
        /* IE */
        -moz-transform: scale(2);
        /* FF */
        -webkit-transform: scale(2);
        /* Safari and Chrome */
        -o-transform: scale(2);
        /* Opera */
        padding: 10px;
        margin-top: 0px;
    }

    a {
        cursor: pointer; 
    }
</style>
<div class="row">
    <div class="col-md-8">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3><?= Html::encode($this->title) ?></h3>
                <h4>Escola: <?= Html::encode($model->escola->nome) ?></h4>
                <h4>Período: <?=  $model->inicio ? date("d/m/Y", strtotime($model->inicio)) : ''; ?> - <?=  $model->fim ? date("d/m/Y", strtotime($model->fim)) : ''; ?></h4>
                <h4>Status: <?= SolicitacaoCredito::ARRAY_STATUS[$model->status]; ?></h4>
                <?php if($model->dataTransferencia): ?>
                    <h4>Data da transferência: <?= date("d/m/Y", strtotime($model->dataTransferencia)) ?></h4>
                <?php endif; ?>
                <?php if($model->valorTransferido): ?>
                    <h4>Valor transferido: <?= \Yii::$app->formatter::DoubletoReal($model->valorTransferido) ?></h4>
                <?php endif; ?>
                <?php
                switch ($model->status) {
                    case SolicitacaoCredito::STATUS_EFETIVADA:
                        if (Usuario::permissao(Usuario::PERFIL_DIRETOR)) {
                            echo '<div class="box-header with-border">';
                            echo Html::button('DEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO_DIRETOR]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                            echo Html::button('INDEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'INDEFERIR', 'class' => 'showModalButton btn btn-danger pull-right']);
                            echo '</div>';
                        }
                        break;

                    case SolicitacaoCredito::STATUS_DEFERIDO_DIRETOR:
                        if (Usuario::permissoes([Usuario::PERFIL_TESC_PASSE_ESCOLAR, Usuario::PERFIL_SUPER_ADMIN])) {
                            //actionAlteracaoStatusAjax
                            echo '<div class="box-header with-border">';
                            echo Html::button('DEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                            echo Html::button('INDEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'INDEFERIR', 'class' => 'showModalButton btn btn-danger pull-right']);
                            echo '</div>';
                        }
                        if (Usuario::permissoes([Usuario::PERFIL_DRE])) {
                            //actionAlteracaoStatusAjax
                            echo '<div class="box-header with-border">';
                            echo Html::button('DEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO_DRE]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                            echo Html::button('INDEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'INDEFERIR', 'class' => 'showModalButton btn btn-danger pull-right']);
                            echo '</div>';
                        }
                        break;


                    case SolicitacaoCredito::STATUS_DEFERIDO_DRE:
                        if (Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_PASSE_ESCOLAR])) {
                            //actionAlteracaoStatusAjax
                            echo '<div class="box-header with-border">';
                            echo Html::button('RECEBER', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO]), 'title' => 'RECEBER', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                            echo Html::button('DEVOLVER', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'DEVOLVER', 'class' => 'showModalButton btn btn-danger pull-right']);
                            echo '</div>';
                        }
                        break;
                            case SolicitacaoCredito::STATUS_DEFERIDO: 
                                if (Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_PASSE_ESCOLAR])) { ?>
                                  <?php $form = ActiveForm::begin([
                                        'id' => 'formAluno',
                                        'options' => ['enctype' => 'multipart/form-data'],
                                        'encodeErrorSummary' => false,
                                        'errorSummaryCssClass' => 'help-block',
                                         'action' => Yii::$app->urlManager->createUrl(['solicitacao-credito/relatorio-final', 'id' => $model->id])

                                    ]); ?>
                                   <div class="row">
                                       <?php if(!$model->dataTransferencia): ?>
                                       <div class="col-md-2">
                                            <?php 
                                                echo $form->field($model, 'dataTransferencia')->widget(DatePicker::classname(), [
                                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                                    'value' =>  $model->dataTransferencia,
                                                    'options' => ['placeholder' => 'Data',   'id' => 'dataTransferencia',],
                                                    'pluginOptions' => [
                                                      
                                                        'orientation' => 'bottom left',
                                                        'autoclose'=>true,
                                                        'format' => 'dd/mm/yyyy',
                                                        // 'startDate' => 'today',
                                                    ]
                                                ]);
                                            ?>
                                       </div>
                                        <?php  endif; ?>
                                        <?php if(!$model->valorTransferido): ?>
                                        <div class="col-md-2">
                                            <?= $form->field($model, 'valorTransferido')->textInput(['id' => 'valorTransferido','maxlength' => true, 'class' => 'form-control money']); ?>

                                        </div> 
                                        <?php  endif; ?>
                                        <?php if(!$model->valorTransferido || !$model->dataTransferencia): ?>
                                        <div class="col-md-1">
                                            <Br><Br>
                                            <?= Html::submitButton('Salvar', ['class' =>  'btn btn-success', 'disabled' => true, 'id' => 'salvarTransferencia']) ?>
                                        </div> 
                                        <?php  endif; ?>
                                   </div> 
                                   <?php ActiveForm::end(); ?>  
                                <?php
                                }
                            break;
                        }
                ?>
            </div>
            <?php $form = ActiveForm::begin(); ?>
            <div class="box-body">
                <h3>Passe escolar</h3>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-striped table-bordered" 3>
                            <thead>
                                <tr>
                                    <th>Benefício</th>
                                    <th>Nome</th>
                                    <th>Passe escolar</th>
                                    <th>Saldo</th>
                                    <th>Justificativa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalValorReal = 0.0;
                                $totalSaldoEscolar = 0.0;
                                if ($passeEscolar) {
                                    // print_r($passeEscolar);

                                    foreach ($passeEscolar as $aluno) {

                                        print '<tr class="aluno-linha">';
                                        print '<td align="center"><div class="checkbox"><label ><input checked  type="checkbox" disabled="true"> </label></div></td>';
                                        print  td($aluno->aluno->nome);
                                        print  td($aluno->valorReal);
                                        print  td($aluno->saldoReal);
                                        print  td($aluno->justificativa);
                                        print '</tr>';
                                        //print'<td align="center"><div class="checkbox"><label ><input name="Checkbox" type="checkbox"  checked readonly="true"></label></div></td>';
                                        //       print td('<span class="aluno">'.$aluno->nome.'</span>');
                                        //       print td('<input class="form-control money passe passeEscolar" type="text" name="passeEscolar['.$aluno->id.'][Valor]" value="'.$aluno->passeEscolar.'" readonly="true">');
                                        //       print td('<input class="form-control money passe saldo" type="text" name="passeEscolar['.$aluno->id.'][Saldo]" value="0">');
                                        //       print td('<input class="form-control" type="text" name="passeEscolar['.$aluno->id.'][Justificativa]" value="">');
                                        // print td('<a class="btn btn-primary pull-right consultarCredito"><i class="fas fa-search"></i></a>');
                                        // //print td('<a class="">Consultar</a> ');
                                        $totalSaldoTransporte += $aluno->saldo;
                                        $totalValeTransporte += $aluno->valor;
                                    }
                                    print '<tr>';
                                    print '<td><b>Valores Totais </b></td>';
                                    print '<td></td>';
                                    print '<td>' . str_replace(".", ",", number_format($totalValeTransporte, 2)) . '</td>';
                                    print '<td>' . str_replace(".", ",", number_format($totalSaldoTransporte, 2)) . '</td>';
                                    print '<td></td>';
                                    print '</tr>';
                                } else {
                                    print '<tr><td colspan="5">Nenhum aluno com benefício ativo.</td></tr>';
                                }


                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h3>Vale transporte</h3>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Benefício</th>
                                    <th>Nome</th>
                                    <th>Vale transporte</th>
                                    <th>Saldo</th>
                                    <th>Justificativa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalSaldoTransporte = 0;
                                $totalValeTransporte = 0;
                                if ($valeTransporte) {
                                    foreach ($valeTransporte as $aluno) {

                                        print '<tr class="aluno-linha">';
                                        print '<td align="center"><div class="checkbox"><label ><input checked  type="checkbox" disabled="true"> </label></div></td>';
                                        print  td($aluno->aluno->nome);
                                        print  td($aluno->valorReal);
                                        print  td($aluno->saldoReal);
                                        print  td($aluno->justificativa);
                                        print '</tr>';
                                        // print '<tr class="aluno-linha">';
                                        // print '<input type="hidden" class="id-aluno" value="'.$aluno->id.'" name="alunoValeTransporte[]" />';
                                        // print'<td align="center"><div class="checkbox"><label ><input checked  type="checkbox" disabled="true"> </label></div></td>';
                                        // print td('<span class="aluno">'.$aluno->nome.'</span>');
                                        // print td('<input class="form-control" type="text" name="valeTransporte['.$aluno->id.'][Valor]" value="'.$aluno->valeTransporte.'">');
                                        // print td('<input class="form-control money" type="text" name="valeTransporte['.$aluno->id.'][Saldo]" value="">');
                                        // print td('<input class="form-control" type="text" name="valeTransporte['.$aluno->id.'][Justificativa]" value="">');
                                        // print td('<a class="btn btn-primary pull-right consultarCredito"><i class="fas fa-search"></i></a>');
                                        // print '</tr>';
                                        $totalSaldoTransporte += $aluno->saldo;
                                        $totalValeTransporte += $aluno->valor;
                                    }
                                    print '<tr>';
                                    print '<td><b>Valores Totais </b></td>';
                                    print '<td></td>';
                                    print '<td>' . str_replace(".", ",", number_format($totalValeTransporte, 2)) . '</td>';
                                    print '<td>' . str_replace(".", ",", number_format($totalSaldoTransporte, 2)) . '</td>';
                                    print '<td></td>';
                                    print '</tr>';
                                } else {
                                    print '<tr><td colspan="5">Nenhum aluno com benefício ativo.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h3>Crédito administrativo</h3>
                        <h4><?= $model->creditoAdministrativoReal; ?></h4>
                    </div>
                </div>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-solid">
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


</div>

<script>
$("#dataTransferencia").change(function(){
    habilitarTransferencia()
})
$("#valorTransferido").keyup(function(){
   // setTimeout(() => habilitarTransferencia(), 500)
   habilitarTransferencia()
})
function habilitarTransferencia(){
    
    $("#salvarTransferencia").prop('disabled', true);
    if(dataTransferencia.value){
        try {
            var dateString = dataTransferencia.value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            var d = new Date( dateString[3], dateString[2]-1, dateString[1] );
            // console.log(d)
        } catch(e){
            console.error(e)
            dataTransferencia.value = ""
            return Swal.fire(
                '',
                'Selecione uma data válida',
                'error'
            )
        }
    }
    console.warn(valorTransferido.value,dataTransferencia.value)
    if(valorTransferido.value && dataTransferencia.value){
        $("#salvarTransferencia").prop('disabled', false);
    }
}
</script>