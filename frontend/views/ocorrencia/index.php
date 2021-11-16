<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper; 
use common\models\Condutor;
use common\models\CondutorRota;
use common\models\Veiculo;
use common\models\Justificativa;
use kartik\daterange\DateRangePicker;
use common\models\Ocorrencia;
/* @var $this yii\web\View */
/* @var $searchModel common\models\OcorrenciaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ocorrências';
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
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'data',
                            'value' => function($model) {
                                $d = new DateTime($model->data);
                                return $d->format('d/m/Y H:i');
                            },
                            // 'format' => ['date', 'php:d/m/Y H:i'],

                            // 'filterType' => GridView::FILTER_DATE_RANGE,
                            'filter' => DateRangePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'data',
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'd/m/Y',
                                    ],
                                ],
                            ]),
                        ],
                        [
                            'attribute' => 'idCondutor',
                            'value' => function($model){
                                return $model->condutor ? $model->condutor->nome : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                                
                            ]
                        ],
                        [
                            'attribute' => 'idCondutorRota',
                            'value' => function($model){
                                return $model->condutorRota->id;
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(CondutorRota::find()->all(), 'id', 'nomeRota'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                                
                            ]
                        ],
                        [
                            'attribute' => 'idJustificativa',
                            'value' => function($model){
                                return $model->justificativa->nome;
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(Justificativa::find()->all(), 'id', 'nome'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                                
                            ]
                        ],
                        
                        // 'idVeiculo',
                         [
                            'attribute' => 'idVeiculo',
                            'label' => 'Veículo',
                            'value' => function($model){
                                return $model->veiculo->placa;
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(Veiculo::find()->all(), 'id', 'placa'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                       
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => Ocorrencia::permissaoActions() 
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>