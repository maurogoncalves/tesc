<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

use common\models\Condutor;
use common\models\CondutorRota;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper; 
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CondutorRotaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rotas';
$this->params['breadcrumbs'][] = $this->title;
function mountCondutores(){
    $condutores = ArrayHelper::map(Condutor::find()->all(), 'id', 'nome');
    $condutores[0] = 'Sem condutor';
    return $condutores;
}
?>   
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
          <div class="box-header with-border">
            <?= CondutorRota::permissaoCriar() ? Html::a('Nova Rota', ['create'], ['class' => 'btn btn-success pull-right']) : ''; ?>
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
        // 'toolbar' =>  [
        //     '{export}{toggleData}',
        // ],
        'exportConfig' => [
            GridView::HTML => true,
            GridView::CSV => true,
            GridView::TEXT => true,
            GridView::EXCEL => true
        ],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
             [
                'attribute' => 'idCondutor',
                'value' => function($model){
                    return $model->condutor ? $model->condutor->nome.'-'.$model->condutor->status : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => mountCondutores(), 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => [
                    'placeholder' => '-',
                    
                ]
            ],
            [
                'attribute' => 'turno',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => CondutorRota::ARRAY_TURNOS, 
                'value'=>  function($model){
                        return $model->turno ? CondutorRota::ARRAY_TURNOS[$model->turno] : '-';
                },
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => [
                    'placeholder' => '-',
                ],
            ],
            [
                'attribute' => 'viagem',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => CondutorRota::ARRAY_VIAGEM, 
                'value'=>  function($model){
                        return $model->viagem ? CondutorRota::ARRAY_VIAGEM[$model->viagem] : '-';
                },
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => [
                    'placeholder' => '-',
                ],
            ],  
            
            'descricao',
            // [
            //     'attribute' => 'turno',
            //     'label' => 'Turno',
            //     'filterType' => GridView::FILTER_SELECT2,
            //     'filter' => CondutorRota::ARRAY_TURNOS, 
            //     'value' => function($model) {
            //         return $model->turno ?  CondutorRota::ARRAY_TURNOS[$model->turno] : '-';
            //     },
            //     'filterWidgetOptions' => [
            //         'pluginOptions' => ['allowClear' => true],
            //     ],
            //     'filterInputOptions' => [
            //         'placeholder' => '-',
            //     ],
           
            // ],
              [
                'attribute' => 'sentido',
                'label' => 'Sentido',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => CondutorRota::ARRAY_SENTIDO, 
                'value'=>  function($model){
                        return $model->sentido ? CondutorRota::ARRAY_SENTIDO[$model->sentido] : '-';
                },
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => [
                    'placeholder' => '-',
                ],
            ],
            [
                'attribute' => 'escolas',
                'format' => 'raw',
                'label' => 'Escola(s) na Viagem',
                'value' => function($model) {
                    $escolas = '<ul>';
                    foreach ($model->escolaPonto as $escola)
                    {
                        $escolas .= '<li>'.$escola->escola->nome.'</li>';
                    }
                    $escolas .= '</ul>';

                    return $escolas;
                }
            ],
            [
                    'attribute' => 'capacidadeVeiculoCondutor',
                    'label' => 'Capacidade do veículo',
                    'value' => function($model) {
                        return $model->condutor ? $model->condutor->veiculo->capacidade :  '-';
                    },
            ],
            [
                'label' => 'Assentos livres',
                'value' => function($model){
                    return $model->condutor ? $model->condutor->veiculo->capacidade - count($model->alunoPonto) : '-';
                },
            ],
            // 'entrada',
            // 'saida',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna                
                'class' => 'yii\grid\ActionColumn',
                'template' => CondutorRota::permissaoActions(), 
                'buttons' => [
                'roterizar' => function ($url, $model) {
                    return  Html::a('<i class="fa fa-street-view" aria-hidden="true"></i>', Url::to(['roterizar', 'idCondutorRota' => $model->id]), ['data-pjax' => 0,'target' => '_blank', 'title' => Yii::t('app', 'Rota'),
                        ]);
                },
                ]
            ]
        ],
    ]); ?>
<?php Pjax::end(); ?>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
		function gerenciadorPdf(){
			window.open('index.php?r=condutor-rota/report')
		}
		
		setInterval(() => {
		itens = $("#w2 li");
		item = $("#w2 li")[itens.length-1];
		if($(item).prop('title') != 'Portable Document Format') {
					$("#w2").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')

		}
		}, 500);



	</script>