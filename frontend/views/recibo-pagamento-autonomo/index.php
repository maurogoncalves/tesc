<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\Escola;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use yii\helpers\Url;
use kartik\date\DatePicker;
use common\models\Usuario;
use common\models\UsuarioGrupo;
use common\models\ReciboPagamentoAutonomo;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AlunoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPA';
$this->params['breadcrumbs'][] = $this->title;

$arrayAnos = [];
for ($i = intval(date('Y')) - 1; $i <= intval(date('Y')); $i++)
    $arrayAnos[$i] = $i;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">


            <div class="box-header with-border">
                <?= Usuario::permissao(Usuario::PERFIL_SUPER_ADMIN) || UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_FINANCEIRO) ? Html::a('Novo Recibo', ['create'], ['class' => 'btn btn-success pull-right']) : ''; ?>
            </div>

            <div class="box-body">
                <?php // Pjax::begin(); 
                ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        // ['class' => 'yii\grid\SerialColumn'],

                        // 'id',
                        // 'numRecibo',
                        [
                            'contentOptions' => ['style' => 'min-width:300px;'],  //Largura                 
                            'attribute' => 'idCondutor',
                            'value' => function ($model) {
                                return $model->condutor->nome; //Yii::t('app', $model->escola->nome);
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
                            'attribute' => 'mes',
                            'value' => 'nomeMes',
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ReciboPagamentoAutonomo::ARRAY_MESES,
                        ],
                        [
                            'attribute' => 'ano',
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => $arrayAnos,
                        ],
                        'quantidade',
                        'diasLetivos',
                        [
                            'attribute' => 'valor',
                            'format' => ['decimal', 2],
                        ],
                        [
                            'attribute' => 'data',
                            'label' => 'Data',
                            'value' => function ($model, $index, $widget) {
                                return ($model->data) ? Yii::$app->formatter->asDate($model->data, 'dd/MM/Y') : '';
                            },

                            'filterType' => GridView::FILTER_DATE,
                            'filterWidgetOptions' => [
                                'pluginOptions' => [
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'format' => 'dd/mm/yyyy',
                                    'autoclose' => true,
                                    'todayHighlight' => true,
                                    'orientation' => 'bottom left',

                                ]
                            ],

                        ],
                        // ['class' => 'yii\grid\ActionColumn'],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => Usuario::permissao(Usuario::PERFIL_SUPER_ADMIN) || UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_FINANCEIRO) ? '{pdf} {delete}' : '{view} {pdf}',
                            'buttons' => [
                                'pdf' => function ($url, $model) {
                                    return  Html::a('<i class="fa fa-file" aria-hidden="true"></i>', Url::to(['pdf/rpa', 'idRecibo' => $model->id, 'pdf' => 1]), [
                                        'data-pjax' => 0,
                                        'target' => '_blank',
                                        'title' => Yii::t('app', 'Gerar relatório'),
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        $url,
                                        [
                                            'data' => [
                                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                                'method' => 'post',
                                                'pjax' => 0,
                                                'ok' => Yii::t('yii', 'Confirm'),
                                                'cancel' => Yii::t('yii', 'Cancel'),
                                            ],
                                        ]
                                    );
                                },

                            ]
                        ]
                    ],
                ]); ?>
                <?php // Pjax::end(); 
                ?>
            </div>
        </div>
    </div>
</div>