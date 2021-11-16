<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper; 
use kartik\daterange\DateRangePicker;
use common\models\Aluno;
use common\models\Condutor;
use kartik\select2\Select2;
use common\models\Justificativa;
use common\models\Comunicado;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ComunicadoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comunicados';
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
                            'format' => ['date', 'php:d/m/Y H:i'],

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
                            'attribute' => 'enviadoPor',
                            'value' => function($model){
                                return $model->enviadoPor ? Comunicado::ARRAY_ENVIADO[$model->enviadoPor] : '-';

                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' =>  Comunicado::ARRAY_ENVIADO, 
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
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>