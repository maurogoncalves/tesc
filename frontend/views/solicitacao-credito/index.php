<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use common\models\Escola;
use common\models\ReciboPagamentoAutonomo;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\SolicitacaoCredito;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoCreditoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitação de crédito';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">

            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'tipoSolicitacao',
                            'value' => function($model) {
                                return $model->tipoSolicitacao ? SolicitacaoCredito::TIPO[$model->tipoSolicitacao] : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' =>  SolicitacaoCredito::TIPO, 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function($model) {
                                return $model->status ? SolicitacaoCredito::ARRAY_STATUS[$model->status] : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' =>  SolicitacaoCredito::ARRAY_STATUS, 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                        [
                            'attribute' => 'idEscola',
                            'value' => function($model){
                                return $model->escola->nome;//Yii::t('app', $model->escola->nome);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(Escola::escolasPerfis($model->escola), 'id', 'nome'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                                
                            ]
                        ],
                        [
                            'attribute' => 'mesInicio',
                            'value' => function($model) {
                                return $model->mesInicio ? ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesInicio] : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ReciboPagamentoAutonomo::ARRAY_MESES, 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                        [
                            'attribute' => 'mesFim',
                            'value' => function($model) {
                                return $model->mesFim ? ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesFim] : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' =>  ReciboPagamentoAutonomo::ARRAY_MESES, 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                        // [
                        //     'class' => '\kartik\grid\DataColumn',
                        //     'attribute' => 'inicio',
                        //     'format' => ['date', 'php:d/m/Y'],
                        //     'filter' => DateRangePicker::widget([
                        //         'model' => $searchModel,
                        //         'attribute' => 'inicio',
                        //         'convertFormat' => true,
                        //         'pluginOptions' => [
                        //             'locale' => [
                        //                 'format' => 'd/m/Y',
                        //             ],
                        //         ],
                        //     ]),
                        // ], 
                        // [
                        //     'class' => '\kartik\grid\DataColumn',
                        //     'attribute' => 'fim',
                        //     'format' => ['date', 'php:d/m/Y'],
                        //     'filter' => DateRangePicker::widget([
                        //         'model' => $searchModel,
                        //         'attribute' => 'fim',
                        //         'convertFormat' => true,
                        //         'pluginOptions' => [
                        //             'locale' => [
                        //                 'format' => 'd/m/Y',
                        //             ],
                        //         ],
                        //     ]),
                        // ],  
                        [
                            'contentOptions' => ['style' => 'min-width:100px;'],  //Largura coluna
                            'class' => 'yii\grid\ActionColumn',
                            'template' => SolicitacaoCredito::permissaoActions(), 
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    if($model->tipoSolicitacao > 0){
                                        $url = Url::toRoute(['solicitacao-credito/relatorio-final', 'id' =>  $model->id]);
                                    }
                                    if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_CREDITO_ADMINISTRATIVO){
                                        $url = Url::toRoute(['solicitacao-credito/credito-administrativo', 'id' =>  $model->id]);
                                    }
                                
                                    return  Html::a('<i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>', $url, [
                                        'title' => Yii::t('app', 'Editar'),
                                        ]) ;
                                },
                                'relatorio' => function ($url, $model) {
									if(($model->tipoSolicitacao == 1) &&($model->status == 1)){
                                        $url = Url::toRoute(['solicitacao-credito/credito-preenchimento', 'id' =>  $model->id]);
                                    }
                                    return $model->status == SolicitacaoCredito::STATUS_EM_ANDAMENTO ? Html::a('<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>', $url, [
                                        'title' => Yii::t('app', 'Editar'),
                                        ]) : '';
                                },
                                'delete' => function($url, $model){
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,
                                        [                                    
                                            'data' => [
                                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                                'method' => 'post',
                                                'pjax' => 0,                                            
                                                'ok' => Yii::t('yii', 'Confirm'),
                                                'cancel' => Yii::t('yii', 'Cancel'),
                                            ],
                                        ]);
                                },
								'download' => function ($url, $model) {     
									if($model->status == 3){
										return Html::a('<span class="glyphicon glyphicon-ok-circle" style="color:#00FF7F!important"></span>', '', ['title' => Yii::t('app', 'Fluxo Finalizado'),]);                                
                                    }
                                  
                                }
                            ]
                        ]
                    ],
                ]); ?>
           </div>
        </div>
    </div>
</div>