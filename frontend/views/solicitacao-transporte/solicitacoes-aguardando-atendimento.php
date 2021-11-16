<?php
use common\models\SolicitacaoCredito;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use common\models\Escola;
use common\models\EscolaHomologacao;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

use common\models\Condutor;
use common\models\Configuracao;
use common\models\UsuarioGrupo;
/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoTransporteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitações Aguardando Atendimento ';
$this->params['breadcrumbs'][] = 'Solicitações Aguardando Atendimento';
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
                    'columns' => [                     
                        [
                            'class'=>'\kartik\grid\DataColumn',
                            'attribute'=>'id',
                            'filterInputOptions' => ['type' => 'number', 'class' => 'form-control'],
                            'contentOptions' => array('style' => 'min-width:70px;'),
                        ],
                        [
                            'attribute' => 'anoVigente',
                        ],   
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'data',
                            // 'filterType' => GridView::FILTER_DATE,
                            // 'value' => function($model) {
                            //     $d = new DateTime($model->data);
                            //     return $d->format('d/m/Y H:i');
                            // },
                            'format' => ['date', 'php:d/m/Y'],

                            // 'filterType' => GridView::FILTER_DATE_RANGE,
                            'filter' => DateRangePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'data',
                                'convertFormat' => true,
                                 'pluginEvents' => [
                                    "show.daterangepicker" => "function(ev, picker) { picker.autoUpdateInput = true; }",
                                    'cancel.daterangepicker'=>"function(ev, picker) { clearInputs2(ev); }"
                                ],
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'd/m/Y',
                                    ],
                                ],
                            ]),
                            'contentOptions' => array('style' => 'width:150px;'),
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'ultimaMovimentacao',
                            'format' => ['date', 'php:d/m/Y'],
                           
                            'filter' => DateRangePicker::widget([
                                'pluginEvents' => [
                                    "show.daterangepicker" => "function(ev, picker) { picker.autoUpdateInput = true; }",
                                    'cancel.daterangepicker'=>"function(ev, picker) { clearInputs(ev); }"
                                ],
                                'model' => $searchModel,
                                'attribute' => 'ultimaMovimentacao',
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'd/m/Y',
                                    ],
                                ],
                            ]),
                            'contentOptions' => array('style' => 'width:150px;'),
                        ],
                        [
                            'attribute' => 'novaSolicitacao',
                            'label' => 'Categoria da solicitação',
                            'value' => function($data) {
                                return $data ? SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO[$data->novaSolicitacao] : '-';
                            },
                            'filter' => SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO
                        ],
                        [
                            'attribute' => 'tipoSolicitacao',
                            'filter' => SolicitacaoTransporte::ARRAY_TIPO_SOLICITACAO,
                            'value' => function($data) {
                                return SolicitacaoTransporte::ARRAY_TIPO_SOLICITACAO[$data->tipoSolicitacao];
                            }
                        ],
                        [
                            'attribute' => 'tipoFrete',
                            'label' => 'Tipo de frete',
                            'value' => function($data) {
                                return $data->tipoFrete ? SolicitacaoTransporte::ARRAY_TIPO_FRETE[$data->tipoFrete] : '-';
                            },
                            'filter' => SolicitacaoTransporte::ARRAY_TIPO_FRETE
                        ],
                        [
                            'attribute' => 'modalidadeBeneficio',
                            'label' => 'Modalidade',
                            'value' => function($data) {
                                return $data ? Aluno::ARRAY_MODALIDADE[$data->modalidadeBeneficio] : '-';
                            },
                            'filter' => Aluno::ARRAY_MODALIDADE
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function($model){
                                return $model->status ? SolicitacaoTransporte::ARRAY_STATUS[$model->status] : '-';

                            },
                            'contentOptions' => ['style' => 'min-width:150px;'],
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' =>  SolicitacaoTransporte::ARRAY_STATUS, 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                                
                            ]
                        ],
                        [
                            'attribute' => 'idAluno',
                            'value' => function($model){
                                return $model->aluno->nome;//Yii::t('app', $model->escola->nome);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(Aluno::find()->all(), 'id', 'nome'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                        [
                            'headerOptions' => ['style' => 'width:200px'],
                            'attribute' => 'idEscola',
                            'value' => function($model){
                                return $model->escola->nomeCompleto;//Yii::t('app', $model->escola->nome);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(Escola::find()->all(), 'id', 'nomeCompleto'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [ 
                                'placeholder' => '-',
                                
                            ]
                        ],
                        [
                            'label' => 'Necessidades especiais',
                            'attribute' => 'necessidadeEspecial',
                            'filter' =>  [1 => 'NÃO', 2 => 'SIM'],
                            'value' => function ($model) {
                                $listaNecessidade = [];
                                foreach ($model->aluno->necessidades as $tipoNecessidade)
                                {
                                    $listaNecessidade[] = $tipoNecessidade->necessidadesEspeciais->nome;
                                }

                                return implode (',', $listaNecessidade);
                            },
                        ],
                        [
                            'headerOptions' => ['style' => 'width:200px'],
                            'attribute' => 'grupo',
                            'value' => function($model){
                               return UsuarioGrupo::grupoSolicitacao($model);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => UsuarioGrupo::ARRAY_GRUPOS_SEM_FINANCEIRO, 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [ 
                                'placeholder' => '-',
                                
                            ]
                        ],
                        // [
                        //     'attribute' => 'condutorIdaNome',
                        //     'label' => 'Condutor Ida',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaIda ? $model->rotaIda->condutor->nome : '-';
                        //     }
                        // ],
                        // // [
                        // //     'attribute' => 'condutorIdaAlvara',
                        // //     'label' => 'Alvará',
                        // //     'value' => function($model){
                        // //         return $model->rotaIda ? $model->rotaIda->condutor->alvara : '-';
                        // //     }
                        // // ],
                        // [
                        //     'attribute' => 'condutorIdaAlvara',
                        //     'label' => 'Alvará',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['>','alvara', 0])->all(), 'id', 'alvara'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaIda ? $model->rotaIda->condutor->alvara : '-';
                        //     }
                        // ],
                        // [
                        //     'attribute' => 'condutorIdaTelefone',
                        //     'label' => 'Telefone',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['is not', 'telefone', null])->all(), 'id', 'telefone'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaIda ? $model->rotaIda->condutor->telefone : '-';
                        //     }
                        // ],


                        // [
                        //     'attribute' => 'condutorVoltaNome',
                        //     'label' => 'Condutor Volta',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaVolta ? $model->rotaVolta->condutor->nome : '-';
                        //     }
                        // ],
                        // // [
                        // //     'attribute' => 'condutorIdaAlvara',
                        // //     'label' => 'Alvará',
                        // //     'value' => function($model){
                        // //         return $model->rotaIda ? $model->rotaIda->condutor->alvara : '-';
                        // //     }
                        // // ],
                        // [
                        //     'attribute' => 'condutorVoltaAlvara',
                        //     'label' => 'Alvará',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['>','alvara', 0])->all(), 'id', 'alvara'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaVolta ? $model->rotaVolta->condutor->alvara : '-';
                        //     }
                        // ],
                        // [
                        //     'attribute' => 'condutorVoltaTelefone',
                        //     'label' => 'Telefone',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['is not', 'telefone', null])->all(), 'id', 'telefone'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaVolta ? $model->rotaVolta->condutor->telefone : '-';
                        //     }
                        // ],
                        [
                           'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna 
                           'class' => 'yii\grid\ActionColumn',
                           'template' => '{view} ',
                           'buttons' => []
                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function clearInputs(e){
        e.stopPropagation();
        e.preventDefault();
        $('#solicitacaotransportesearch-ultimamovimentacao').val(''); 
        $('input[name="daterangepicker_start"]').val("");
        $('input[name="daterangepicker_end"]').val("");
                let params =window.location.href
             params = params.split('&')
            let finalLocation = ''
            for(let i=0; i<=params.length; i++){
                let param = params[i];
                if(param && param.startsWith('SolicitacaoTransporteSearch') && !param.startsWith('SolicitacaoTransporteSearch%5BultimaMovimentacao')){
                    finalLocation += '&'+param
                }
            }
            // console.warn(finalLocation)
            window.location.href = 'index.php?r=solicitacao-transporte%2Fsolicitacoes-aguardando-atendimento'+finalLocation;

    }

        function clearInputs2(e){
        e.stopPropagation();
        e.preventDefault();
        $('#solicitacaotransportesearch-data').val(''); 
        $('input[name="daterangepicker_start"]').val("");
        $('input[name="daterangepicker_end"]').val("");
                let params =window.location.href
             params = params.split('&')
            let finalLocation = ''
            for(let i=0; i<=params.length; i++){
                let param = params[i];
                if(param && param.startsWith('SolicitacaoTransporteSearch') && !param.startsWith('SolicitacaoTransporteSearch%5Bdata')){
                    finalLocation += '&'+param
                }
            }
            // console.warn(finalLocation)
            window.location.href = 'index.php?r=solicitacao-transporte%2Fsolicitacoes-aguardando-atendimento'+finalLocation;

    }
</script>
