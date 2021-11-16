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
use common\models\AgrupamentoBairro;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoTransporteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agrupamento de bairros';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">

    <div class="col-md-12">
        <div class="box box-solid">
        <div class="box-header with-border">
            <?= AgrupamentoBairro::permissaoCriar() ? Html::a('Novo Agrupamento', ['create'], ['class' => 'btn   align-button btn-success pull-right']) : ''; ?>
            <?= Html::a('Emitir Relatório', ['relatorio'], ['class' => 'btn btn-primary pull-right ']); ?>

        </div>
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [                     
                        // [
                        //     'class'=>'\kartik\grid\DataColumn',
                        //     'attribute'=>'id',
                        //     'filterInputOptions' => ['type' => 'number', 'class' => 'form-control'],
                        //     'contentOptions' => array('style' => 'min-width:70px;'),
                        // ],
                        // [
                        //     'attribute' => 'anoVigente',
                        // ],   
                        // [
                        //     'class' => '\kartik\grid\DataColumn',
                        //     'attribute' => 'data',
                        //     // 'filterType' => GridView::FILTER_DATE,
                        //     // 'value' => function($model) {
                        //     //     $d = new DateTime($model->data);
                        //     //     return $d->format('d/m/Y H:i');
                        //     // },
                        //     'format' => ['date', 'php:d/m/Y'],

                        //     // 'filterType' => GridView::FILTER_DATE_RANGE,
                        //     'filter' => DateRangePicker::widget([
                        //         'model' => $searchModel,
                        //         'attribute' => 'data',
                        //         'convertFormat' => true,
                        //         'pluginOptions' => [
                        //             'locale' => [
                        //                 'format' => 'd/m/Y',
                        //             ],
                        //         ],
                        //     ]),
                        //     'contentOptions' => array('style' => 'width:150px;'),
                        // ],
                        // 'id',
                        'idBairro',
                        'nome', 
                        [
                            'attribute' => 'agrupamento',
                            'value' => function($data) {
                                return $data ? AgrupamentoBairro::ARRAY_BAIRRO[$data->agrupamento] : '-';
                            },
                            'filter' => AgrupamentoBairro::ARRAY_BAIRRO
                        ],
                        [
                           'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna 
                           'class' => 'yii\grid\ActionColumn',
                           'template' => '{update} {delete} ',
                           'buttons' => []
                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>


