<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use common\models\Aviso;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\SolicitacaoCredito;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoCreditoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Avisos';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <?= Aviso::permissaoCriar() ? Html::a('Novo Aviso', ['create'], ['class' => 'align-button btn btn-success pull-right ']) : ''; ?>
                <?= Html::a('Visualizar avisos', ['aviso/meus-avisos'], ['class' => 'btn btn-primary pull-right ']) ?>
            </div>
            <div class="box-body">
            <?php Pjax::begin(); ?>  
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        // ['class' => 'yii\grid\SerialColumn'],
                        'id',
                        [
                            'attribute' => 'fixado',
                            'label' => 'Aviso fixado',
                            'value' =>   function($model){
                                return  Aviso::ARRAY_FIXADO[$model->fixado];
                            },
                            'filter' => Aviso::ARRAY_FIXADO
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
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'd/m/Y',
                                    ],
                                ],
                            ]),
                            'contentOptions' => array('style' => 'width:150px;'),
                        ],
                        'titulo',
                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>   
            </div>   
      </div>
    </div>
</div>
