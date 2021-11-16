<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Usuario;
use kartik\grid\GridView;
use kartik\dialog\Dialog;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use common\models\Escola;
use common\models\SolicitacaoStatus;
use yii\data\ArrayDataProvider;
/* @var $this yii\web\View */
/* @var $model common\models\Usuario */

$this->title = 'Solicitação #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Solicitação de transporte', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-5">
        <div class="box box-solid">
            <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    // 'id',
                    // 'idAluno',
                    // 'idEscola',
                    // 'data',
                    // 'status',
                    [
                        'attribute' => 'novaSolicitacao',
                        'label' => 'Categoria da solicitação',
                        'value' => function($model){
                            if(is_null($model->novaSolicitacao))
                                return '-';
                            return $model->novaSolicitacao == 1 ? 'NOVA SOLICITAÇÃO' : 'RENOVAÇÃO DE SOLICITAÇÃO';
                        }
                    ],
                    [
                        'attribute'=>'data',
                        'label' => 'Data',
                        'value' => function ($model) {
                            return ($model->data)?Yii::$app->formatter->asDate($model->data, 'dd/MM/Y'):'';
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
                        'label' => 'Declarações Entregues',
                        'value' => function($model) {
                           if($model->solicitacaoEscolasProximas){
                                foreach ($model->solicitacaoEscolasProximas as $items)
                                    $meus[] = $items->escola->nome;
                                return implode (', ', $meus);
                            } else {
                                return '-';
                            }
                        }
                    ],
                    // [
                    //     'attribute' => 'status',
                    //     'value' => function($model){
                    //         return $model->status ? SolicitacaoTransporte::ARRAY_STATUS[$model->status] : '-';
                    //     },
                    //     'filterType' => GridView::FILTER_SELECT2,
                    //     'filter' =>  SolicitacaoTransporte::ARRAY_STATUS, 
                    //     'filterWidgetOptions' => [
                    //         'pluginOptions' => ['allowClear' => true],
                    //     ],
                    //     'filterInputOptions' => [
                    //         'placeholder' => '-',
                            
                    //     ]
                    // ],
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
                        'label' => 'Último Condutor (Ida)',
                        'value' => function ($model) {
                            return $model->ultimoCondutorIda->nome;
                        },
                        'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                    ],
                    [
                        'label' => 'Telefone do condutor (Ida)',
                        'value' => function ($model) {
                            return $model->ultimoCondutorIda->telefoneValido;
                        },
                        'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                    ],
                    [
                        'label' => 'Último Condutor (Volta)',
                        'value' => function ($model) {
                            return $model->ultimoCondutorVolta->nome;
                            // return $model->aluno->solicitacaoAtiva->rotaVolta ? $model->aluno->solicitacaoAtiva->rotaVolta->condutor->nome.' | Rota '.$model->aluno->solicitacaoAtiva->rotaVolta->id : '-';
                        },
                        'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                    ],
                    [
                        'label' => 'Telefone do condutor (Volta)',
                        'value' => function ($model) {
                            return $model->ultimoCondutorVolta->telefoneValido;
                        },
                        'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                    ],
                    [
                        'attribute' => 'idEscola',
                        'value' => function($model){
                            return $model->escola->nome;//Yii::t('app', $model->escola->nome);
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => ArrayHelper::map(Escola::find()->all(), 'id', 'nome'), 
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => [
                            'placeholder' => '-',
                            
                        ]
                    ],
                    'justificativaBarreiraFisica:ntext',
                    [
                        'attribute' =>'modalidadeBeneficio',
                        'value' => function($model){
                            return Aluno::ARRAY_MODALIDADE[$model->modalidadeBeneficio];
                        },
                    ],
                    'cartaoPasseEscolar',
                    'cartaoValeTransporte',
                    [
                        'attribute' => 'barreiraFisica',
                        'value' => function($model) {
                            return $model->barreiraFisica == 1 ? 'SIM' : 'NÃO';
                        },
                    ],
                    'motivoBarreiraFisica',
                    'distanciaEscola',
                    [
                        'attribute' => 'checkForm',
                        'value' => function ($model){
                            return $model->checkForm ? 'SIM' : 'NÃO';
                        }
                     ],
                     [
                        'attribute' => 'checkInex',
                        'value' => function ($model){
                            return $model->checkInex ? 'SIM' : 'NÃO';
                        }
                     ], 
                     [
                        'attribute' => 'checkEnd',
                        'value' => function ($model){
                            return $model->checkEnd ? 'SIM' : 'NÃO';
                        }
                     ],
                     [
                        'attribute' => 'checkMemorando',
                        'value' => function ($model){
                            return $model->checkMemorando ? 'SIM' : 'NÃO';
                        }
                     ],
                     [
                        'attribute' => 'checkSed',
                        'value' => function ($model){
                            return $model->checkSed ? 'SIM' : 'NÃO';
                        }
                     ],
                     [
                        'attribute' => 'checkVizinho',
                        'value' => function ($model){
                            return $model->checkVizinho ? 'SIM' : 'NÃO';
                        }
                     ], 
                     [
                        'attribute' => 'checkLaudoMedico',
                        'value' => function ($model){
                            return $model->checkLaudoMedico ? 'SIM' : 'NÃO';
                        }
                     ], 
                     [
                        'attribute' => 'checkSolicitacaoEspecial',
                        'value' => function ($model){
                            return $model->checkSolicitacaoEspecial ? 'SIM' : 'NÃO';
                        }
                     ], 
                ],
            ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-7">
      <div class="box box-solid">
            <div class="box-body">
                    <?php


        $tratados =[];
        $historicosTratados = [];
        foreach($model->historico as $historico) {
            if(!isset($tratado[$historico->justificativa.' '.$historico->dataCadastro])) {
                $historicosTratados[] = $historico;
                $tratado[$historico->justificativa.' '.$historico->dataCadastro] = $historico->id;
            }
        }



                 ?>

       <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' =>  $historicosTratados,
                    'key' => 'id',
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]),
                'pjax' => true,
                'pjaxSettings' =>[
                    'neverTimeout'=>true,
                    'options'=>[
                            'id'=>'grid',
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
                        'attribute'=>'dataCadastro',
                        'label' => 'Data',
                        'value' => function ($model, $index, $widget) {
                            return ($model->dataCadastro)?Yii::$app->formatter->asDate($model->dataCadastro, 'dd/MM/Y'):'';
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
                        'value'=>  function($model){
                                return $model->usuario->nome;
                        },
                    ],
                    [
                        'attribute' => 'justificativa',
                        'label' => 'Justificativa',
                        'format' => 'html',
                        'filter' => false,
                        'value'=>  function($model){
                            
                            $response = $model->justificativa;
                            if($model->justificativaSetor)
                                $response .= "<br><b>Justificativa do setor: </b>".$model->justificativaSetor; 
                            return $response;
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Status',
                        'filter' => false,
                        'value' => function($model) {
                            return $model->status ?  SolicitacaoTransporte::ARRAY_STATUS[$model->status] : '-';
                        }
                    ],
                    
                    // [
                    //     'attribute' => 'turno',
                    //     'label' => 'Turno',
                    //     'filter' => false,
                    //     'value' => function($model) {
                    //         return $model->turno ?  CondutorRota::ARRAY_TURNOS[$model->turno] : '-';
                    //     }
                    // ],
                    //   [
                    //     'attribute' => 'sentido',
                    //     'label' => 'Sentido',
                    //     'value'=>  function($model){
                    //             return $model->sentido ? CondutorRota::ARRAY_SENTIDO[$model->sentido] : '-';
                    //     },
                    // ],      
                    // [
                    //        'class' => 'yii\grid\ActionColumn',
                    //        'template' => '{view}',
                    //        'buttons' => [
                    //              'view' => function ($url, $model) {
                    //                 return Html::a('<i class="fa fa-fw fa-trash"></i>', ['condutor-escola/delete', 'id' => $model->id],['data' => ['method' => 'post',]])
                    //                 ;
                                
                    //             },
                         
                    //         ]
                    // ],

                
                ],
            ]); ?>
    </div>
    </div>
    </div>
</div>
<?php 
    echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
    ]);
?>