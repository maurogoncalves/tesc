<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\Escola;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\Configuracao;
use common\models\SolicitacaoTransporte;
use common\models\EscolaAtendimento;
use common\models\Atendimento;
/* @var $this yii\web\View */
/* @var $searchModel common\models\AlunoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Alunos';
$this->params['breadcrumbs'][] = $this->title;

 const ARRAY_TURNO = [
        1 => 'MANHÃ',
        2 => 'TARDE',
        3 => 'NOITE',     
		4 => 'INTEGRAL', 	
    ];
	
?>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4>
                    <?= '<span class="label label-primary">Total: '.$dataProvider->getTotalCount().'</span>'; ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
        
          <div class="box-header with-border">
            <?= Aluno::permissaoCriar() ? Html::a('Novo Aluno', ['create'], ['class' => 'btn btn-success pull-right']) : ''; ?>
        </div>
        <div class="box-body">
             <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panel' => [
                    'heading'=>false,
                    'type'=>false,
                    'showFooter'=>false
                ],
                'summary' => "Exibindo <b>{begin}</b>-<b>{end}</b> de <b>{totalCount}</b> itens.",
                'toolbar' => \Yii::$app->showEntriesToolbar->create(),
                // 'showPageSummary' => true,
                'columns' => [
                // ['class' => 'yii\grid\SerialColumn'],
                // 'id',
                'nome',
				[
                    'attribute' => 'idade',
                    'label' => 'Idade',
                    'value' => function($model){
						$idade      = date("Y") - $model->dataNascimento;
						if (date("m") < $mesNasc){
							$idade -= 1;
						} elseif ((date("m") == $mesNasc) && (date("d") <= $diaNasc) ){
							$idade -= 1;
						}
                        return $idade.' anos';
                    }
                ],
                 // 'dataNascimento',
                 // 'nomeMae',
                 // 'nomePai',
                [
                    'attribute' => 'RA',
                    'label' => 'RA',
                    'value' => function($model){
                        return $model->RA.' '.$model->RAdigito;
                    }
                ],
                [
                    'attribute' => 'serie',
                    'label' => 'Ano/Série',
                    'value' =>   function($model){
                        return  Aluno::ARRAY_SERIES[$model->serie];
                    },
                    'filter' => Aluno::ARRAY_SERIES
                ],
                [
                    'attribute' => 'turma',
                    'label' => 'Turma',
                    'value' =>   function($model){
                        return Aluno::ARRAY_TURMA[$model->turma];
                    },
                    'filter' => Aluno::ARRAY_TURMA
                ],
				[
                    'attribute' => 'turno',
                    'label' => 'Turno',
                    'value' =>   function($model){
                        return Aluno::ARRAY_TURNO[$model->turno];
                    },
					 'filter' => ARRAY_TURNO
                ],
				[
                    'attribute' => 'horarioEntrada',
                    'label' => 'Horário de Entrada',
                    'value' =>   function($model){
                        return $model->horarioEntrada;
                    },
					
                ],
				[
                    'attribute' => 'horarioSaida',
                    'label' => 'Horário de Saída',
                    'value' =>   function($model){
                        return $model->horarioSaida;
                    },
					 
                ],
                [
                    'attribute' => 'idEscola',
                    'value' => function($model){
                        return $model->escola->nome;//Yii::t('app', $model->escola->nome);
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => ArrayHelper::map(Escola::find()->rightJoin('Aluno','Aluno.idEscola=Escola.id')->all(), 'id', 'nome'), 
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => '-',
                    ]
                ],
                // [
                //     'attribute' => 'redeEnsino',
                //     'label' => 'Rede de ensino',
                //     'value' => function($data) {
                //         return Escola::ARRAY_ENSINO[$data->escola->unidade];
                //     },
                //     'filter' => Escola::ARRAY_ENSINO
                // ],
                [
                    'label' => 'Rede de ensino',
                    'attribute' => 'ensino',
                    'filter' =>  Escola::ARRAY_ENSINO,
                    'value' => function ($data) { 
                        return $data->ensino ? Escola::ARRAY_ENSINO[$data->ensino] : '-';
                        // $redes = [];
                        // foreach ($data->escola->atendimento as $rede)
                        // {
                        //     $redes[] = Escola::ARRAY_ENSINO[$rede->idAtendimento];
                        // }

                        // return implode (',', $redes);
                    },
                ],
                [
                    'attribute' => 'modalidadeBeneficio',
                    'label' => 'Modalidade',
                    'value' => function($data) {
                        return $data->solicitacao ? Aluno::ARRAY_MODALIDADE[$data->solicitacao->modalidadeBeneficio] : '-';
                    },
                    'filter' => Aluno::ARRAY_MODALIDADE
                ],
                [
                    'attribute' => 'tipoFrete',
                    'label' => 'Tipo do frete',
                    'value' => function($data) {
                        return $data->solicitacao && $data->solicitacao->tipoFrete ? SolicitacaoTransporte::ARRAY_TIPO_FRETE[$data->solicitacao->tipoFrete] : '-';
                    },
                    'filter' => SolicitacaoTransporte::ARRAY_TIPO_FRETE
                ],
                [
                    'label' => 'Necessidades especiais',
                    'attribute' => 'necessidadeEspecial',
                    'filter' =>  [1 => 'NÃO', 2 => 'SIM'],
                    'value' => function ($model) {
                        $listaNecessidade = [];
                        foreach ($model->necessidades as $tipoNecessidade)
                        {
                            $listaNecessidade[] = $tipoNecessidade->necessidadesEspeciais->nome;
                        }

                        return implode (',', $listaNecessidade);
                    },
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Status',
                    'value' => function($data) {
                        return $data->solicitacao ? SolicitacaoTransporte::ARRAY_STATUS[$data->solicitacao->status] : '-' ;
                    },
                    'filter' => SolicitacaoTransporte::ARRAY_STATUS
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => Aluno::permissaoActions() 
                ],
                ],
            ]); ?>
         

            <?php 
                // $qtdAluno = 0;
                // $volFinanceiro = 0;
                // foreach ($dataProvider->allModels as $data)
                // {
                //     if ($data->solicitacao && $data->solicitacao->modalidadeBeneficio == Aluno::MODALIDADE_PASSE) {
                //         $volFinanceiro += 20 * 2 * Configuracao::setup()->passeEscolar;
                //         $qtdAluno ++;
                //     }
                // }
            ?>
            <!-- <div class="row">
                <div class="col-xs-4">
                    <p class="lead"></p>
                    <div class="table-responsive">
                        <table class="table sumario">
                                <tr>
                                    <td class="titulo" colspan="2" style="text-align: center;">Passe escolar</td>
                                </tr>
                                <tr>
                                    <td class="titulo">Quantidade de aluno</td>
                                    <td><?= $qtdAluno?></td>
                                </tr>
                                <tr>
                                    <td class="titulo">Volume financeiro</td>
                                    <td><?= 'R$ '.Yii::$app->formatter->asDecimal($volFinanceiro, 2)?></td>
                                </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xs-6">

                </div>
            </div> -->
        </div>
    </div>
</div>
</div>