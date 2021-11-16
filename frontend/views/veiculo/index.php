<?php
use yii\helpers\Html;
use kartik\grid\GridView;

use yii\widgets\Pjax;
use common\models\Escola;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Usuario;
use common\models\Condutor;
use common\models\Veiculo;
use common\models\Modelo;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UsuarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Veículos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
     

          <div class="box-header with-border">
            <?= Html::a('Novo Veículo', ['create'], ['class' => 'btn btn-success pull-right']) ?>
        </div>

        <div class="box-body">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
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
                // ['class' => 'yii\grid\SerialColumn'],
                // 'id',
                //'idModelo',

                [
                    'attribute' => 'idCondutor',
                    'label' => 'Condutor',
                    'value' => function($model){
                        return $model->condutor->nome;//Yii::t('app', $model->escola->nome);
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
                    'attribute' => 'idModelo',
                    'label' => 'Modelo',
                    'value' => function($model){
                        return $model->modelo->nome;//Yii::t('app', $model->escola->nome);
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => ArrayHelper::map(Modelo::find()->all(), 'id', 'nome'), 
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => '-',
                    ]
                ],
                'placa',
                [
                    'attribute' => 'tipoVeiculo',
                    'value' => function($model){
                        return Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => Veiculo::ARRAY_TIPO_VEICULO, 
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => '-',
                    ]
                ],
                [
                    'attribute' => 'alocacao',
                    'value' => function($model){
                        return Veiculo::ARRAY_ALOCACAO[$model->alocacao];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => Veiculo::ARRAY_ALOCACAO, 
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => '-',
                    ]
                ],
                'capacidade',
                [
                        'attribute' => 'combustivel',
                        'value' => function($model){
                            return Veiculo::ARRAY_TIPO[$model->combustivel];
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => Veiculo::ARRAY_TIPO,//ArrayHelper::map(Escola::find()->all(), 'id', 'nome'), 
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => [
                            'placeholder' => '-',
                            
                        ]

                ],
                // 'combustivel',
                

                // 'dataVistoriaEstadual',
                // 'dataVistoriaMunicipal',
                // 'dataVencimenoSeguro',

                [

                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete} ',
                    'buttons' => [
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
                    }
                ],
            ],   
        ],
        'exportConfig' => [
            GridView::EXCEL => true
        ],
    ]); ?>
<?php Pjax::end(); ?>    
    </div>
    </div>
</div>
</div>

<script type="text/javascript">

        function gerenciadorXls(){
            let get = window.location.search;

            get = get.replace('veiculo%2Findex', 'veiculo/report-xls');
            
            window.open(get)
        }

        function gerenciadorPdf(){
            let get = window.location.search;

            get = get.replace('veiculo%2Findex', 'veiculo/report-pdf');
            
            window.open(get)
        }
        
        // setInterval(() => {
        itens = $("#w2 li").remove();
        item = $("#w2 li")[itens.length-1];
        // if($(item).prop('title') != 'Portable Document Format') {
            $("#w2").append('<li id="meuXls" title="Excel"><a onclick="gerenciadorXls()" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>')
            $("#w2").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')
        // }
        // }, 500);
</script>