


<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper; 
use kartik\daterange\DateRangePicker;
use common\models\Condutor;
use common\models\CondutorRota;
use common\models\Veiculo;
use kartik\date\DatePicker;
use kartik\widgets\TimePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HistoricoSearchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Histórico de viagem';
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
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'd/m/Y',
                                    ],
                                ],
                            ]),
                        ],
                        [
                            'class'=>'\kartik\grid\DataColumn',
                            'attribute'=>'checkIn',
                            'value' => 'checkIn',
                            'filterInputOptions' => ['type' => 'time', 'class' => 'form-control']
                        ],
                        [
                            'class'=>'\kartik\grid\DataColumn',
                            'attribute'=>'checkOut',
                            'value' => 'checkOut',
                            'filterInputOptions' => ['type' => 'time', 'class' => 'form-control']
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
                           'class' => 'yii\grid\ActionColumn',
                           'template' => '{view} ',
                           'buttons' => []
                        ]
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>



