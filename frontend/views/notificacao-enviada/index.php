<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\Aluno;
use yii\helpers\ArrayHelper;
use common\models\Escola;
use common\models\NotificacaoEnviada;
use common\models\Usuario;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NotificacaoEnviadaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notificações Enviadas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
          
  
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
            //['class' => 'yii\grid\SerialColumn'],
            // 'id',
       
            [
                'attribute' => 'idUsuario',
                'value' => function($model){
                    return $model->usuario->nome;//Yii::t('app', $model->escola->nome);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Usuario::find()->all(), 'id', 'nome'), 
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

            //'idFirebase',
            [
                'attribute' => 'tipo',
                'value' => function($model){
                    return $model->tipo ? NotificacaoEnviada::ARRAY_TIPOS[$model->tipo] : '-';

                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' =>  NotificacaoEnviada::ARRAY_TIPOS, 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => [
                    'placeholder' => '-',
                    
                ]
            ],
            //'texto',

           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
       </div>
    </div>
</div>
