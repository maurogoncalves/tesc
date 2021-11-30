<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use common\models\Condutor;
use common\models\Veiculo;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CondutorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Condutores';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>

</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">  

          <div class="box-header with-border">
            <?= Condutor::permissaoCriar() ?  Html::a('Novo Condutor', ['create'], ['class' => 'btn btn-success pull-right align-button']) : '' ?>
            <?php if(Condutor::permissaoCriar()){  ?>
                <div class="btn-group pull-right" style="display: none;">
                    <button id="w55" class="btn btn-default dropdown-toggle " title="Exportar" data-toggle="dropdown" aria-expanded="false" style="color:#3980D8">Exportar dados  <i class="glyphicon glyphicon-cloud-download"></i>  </button>
                    <ul id="w66" class="dropdown-menu dropdown-menu-right">
                        <li title="Texto Delimitado por Tabulação"><a class="export-txt" onclick='gerenciarExportacao(event,"TXT")' data-mime="text/plain" data-hash="b7d45805ba6739212bd208b8d5896e0ccaad77368c9df96b5dd483320ddecb67gridviewexportar-listagemtext/plainutf-81{&quot;colDelimiter&quot;:&quot;\t&quot;,&quot;rowDelimiter&quot;:&quot;\r\n&quot;}" data-css-styles="[]" tabindex="-1"><i class="text-muted glyphicon glyphicon-floppy-save"></i> Texto</a></li>
                        <li title="Microsoft Excel 95+"><a class="export-xls" onclick='gerenciarExportacao(event,"EXCEL")' data-mime="application/vnd.ms-excel" data-hash="c78def80d35ad515b4ececb6260d2a82230d11149b73a853a3f74d8ea62c7dfcgridviewexportar-listagemapplication/vnd.ms-excelutf-81{&quot;worksheet&quot;:&quot;ExportarPlanilha&quot;,&quot;cssFile&quot;:&quot;&quot;}" data-css-styles="{&quot;.kv-group-even&quot;:{&quot;background-color&quot;:&quot;#f0f1ff&quot;},&quot;.kv-group-odd&quot;:{&quot;background-color&quot;:&quot;#f9fcff&quot;},&quot;.kv-grouped-row&quot;:{&quot;background-color&quot;:&quot;#fff0f5&quot;,&quot;font-size&quot;:&quot;1.3em&quot;,&quot;padding&quot;:&quot;10px&quot;},&quot;.kv-table-caption&quot;:{&quot;border&quot;:&quot;1px solid #ddd&quot;,&quot;border-bottom&quot;:&quot;none&quot;,&quot;font-size&quot;:&quot;1.5em&quot;,&quot;padding&quot;:&quot;8px&quot;},&quot;.kv-table-footer&quot;:{&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-page-summary td&quot;:{&quot;background-color&quot;:&quot;#ffeeba&quot;,&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-align-center&quot;:{&quot;text-align&quot;:&quot;center&quot;},&quot;.kv-align-left&quot;:{&quot;text-align&quot;:&quot;left&quot;},&quot;.kv-align-right&quot;:{&quot;text-align&quot;:&quot;right&quot;},&quot;.kv-align-top&quot;:{&quot;vertical-align&quot;:&quot;top&quot;},&quot;.kv-align-bottom&quot;:{&quot;vertical-align&quot;:&quot;bottom&quot;},&quot;.kv-align-middle&quot;:{&quot;vertical-align&quot;:&quot;middle&quot;},&quot;.kv-editable-link&quot;:{&quot;color&quot;:&quot;#428bca&quot;,&quot;text-decoration&quot;:&quot;none&quot;,&quot;background&quot;:&quot;none&quot;,&quot;border&quot;:&quot;none&quot;,&quot;border-bottom&quot;:&quot;1px dashed&quot;,&quot;margin&quot;:&quot;0&quot;,&quot;padding&quot;:&quot;2px 1px&quot;}}" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>
						<li title="CSV"><a class="export-csv" onclick='gerenciarExportacao(event,"CSV")' data-mime="application/vnd.ms-excel" data-hash="c78def80d35ad515b4ececb6260d2a82230d11149b73a853a3f74d8ea62c7dfcgridviewexportar-listagemapplication/vnd.ms-excelutf-81{&quot;worksheet&quot;:&quot;ExportarPlanilha&quot;,&quot;cssFile&quot;:&quot;&quot;}" data-css-styles="{&quot;.kv-group-even&quot;:{&quot;background-color&quot;:&quot;#f0f1ff&quot;},&quot;.kv-group-odd&quot;:{&quot;background-color&quot;:&quot;#f9fcff&quot;},&quot;.kv-grouped-row&quot;:{&quot;background-color&quot;:&quot;#fff0f5&quot;,&quot;font-size&quot;:&quot;1.3em&quot;,&quot;padding&quot;:&quot;10px&quot;},&quot;.kv-table-caption&quot;:{&quot;border&quot;:&quot;1px solid #ddd&quot;,&quot;border-bottom&quot;:&quot;none&quot;,&quot;font-size&quot;:&quot;1.5em&quot;,&quot;padding&quot;:&quot;8px&quot;},&quot;.kv-table-footer&quot;:{&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-page-summary td&quot;:{&quot;background-color&quot;:&quot;#ffeeba&quot;,&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-align-center&quot;:{&quot;text-align&quot;:&quot;center&quot;},&quot;.kv-align-left&quot;:{&quot;text-align&quot;:&quot;left&quot;},&quot;.kv-align-right&quot;:{&quot;text-align&quot;:&quot;right&quot;},&quot;.kv-align-top&quot;:{&quot;vertical-align&quot;:&quot;top&quot;},&quot;.kv-align-bottom&quot;:{&quot;vertical-align&quot;:&quot;bottom&quot;},&quot;.kv-align-middle&quot;:{&quot;vertical-align&quot;:&quot;middle&quot;},&quot;.kv-editable-link&quot;:{&quot;color&quot;:&quot;#428bca&quot;,&quot;text-decoration&quot;:&quot;none&quot;,&quot;background&quot;:&quot;none&quot;,&quot;border&quot;:&quot;none&quot;,&quot;border-bottom&quot;:&quot;1px dashed&quot;,&quot;margin&quot;:&quot;0&quot;,&quot;padding&quot;:&quot;2px 1px&quot;}}" tabindex="-1"><i class="text-primary glyphicon glyphicon-floppy-open"></i> CSV</a></li>
                        <li title="Portable Document Format"><a class="export-pdf"  tabindex="-1"  onclick='gerenciarExportacao(event,"PDF")'><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>
                    </ul>
                </div>
                <?php }  ?>
        </div>

        <div class="box-body"> 

               <?= GridView::widget([
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
                    [
                        'class' => 'kartik\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'checkboxOptions' =>
                            function($model) {
                                return ['value' => $model->id, 'class' => 'checkbox-row', 'id' => 'checkbox'];
                            },
                        'hAlign'=>'center',
                        'vAlign'=>'middle',
                        'hiddenFromExport'=>true,
                        'mergeHeader'=>true,
                    ],
                    // ['class' => 'yii\grid\SerialColumn'],
                'nome',
                [ 
                    'label' => 'Status',
                    'attribute' => 'status',
                    'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => 'Todos'],

                    'filter' => Condutor::ARRAY_STATUS,
                    'value' => function($model){
                        return  Condutor::ARRAY_STATUS[$model->status];
                    },
                ],

              
          
                [ 
                    'label' => 'Região de atuação',
                    'attribute' => 'regiao',
                    'filter' => Condutor::ARRAY_REGIAO,
                    'value' => function ($data) {
                       return $data->getRegioesAsString();
                    },
                ],
                [
                    'attribute' => 'alvara',
                    'value' => function($data) {
                        return $data->alvara ? $data->alvara :  '-';
                    },
                ],
                [
                    'attribute' => 'capacidadeVeiculoCondutor',
                    'label' => 'Capacidade do veículo',
                    'value' => function($model) {
                        return $model->veiculo ? $model->veiculo->capacidade :  '-';
                    },
                ],
                [
                    'attribute' => 'veiculoAdaptadoCondutor',
                    'label' => 'Veículo adaptado',
                    'value' => function($data) {
                        return $data->veiculo ? Veiculo::ARRAY_ADAPTADO[$data->veiculo->adaptado] :  '-';
                    },
                    'filter' => Veiculo::ARRAY_ADAPTADO
                ],

                [
                'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna                
                'class' => 'yii\grid\ActionColumn',
                'template' => Condutor::permissaoActions(),
                'buttons' => [
                'folhaPonto' => function ($url, $model) {
                    return '';
                    // return  Html::a('<i class="fa fa-address-book" aria-hidden="true"></i>', Url::to(['pdf/folha-ponto', 'pdf' => 1, 'id' => $model->id]), ['data-pjax' => 0,'target' => '_blank', 'title' => Yii::t('app', 'Gerar relatório'),
                    //     ]);
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
                ]
                ]

                ],
                ]); ?>
             
            </div>
        </div>
    </div>
</div>


<script> 
$(document).ready(function() {
setTimeout(() => $("#w2").html($("#w66").html()), 200)

    
});
setTimeout(() => {
    try {
    document.getElementById('w2').innerHTML= document.getElementById('w66').innerHTML
} catch(e) {
    console.log(e);
}
}, 200)

function gerenciarExportacao(event, tipo){
		
		event.preventDefault();
		console.log(1);
		let indexes = [];
		let keys = $('#w0').yiiGridView('getSelectedRows');
        let checkboxes = keys;       
		var status =  document.getElementsByName("CondutorSearch[status]")[0].value;
		let x='todos os registros de condutores?';
		if(checkboxes.length > 0){
			x = checkboxes.length+' registros?';
		} 
		
		console.warn(x)
		Swal.fire({
            title: 'Exportar registros',
            text: "Confirma a exportação de "+x,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO'
          }).then((result) => {
            if (result.value) {
                console.warn(checkboxes)
			  window.open('index.php?r=condutor/exportar-endereco&selecionados='+checkboxes+'&tipo='+tipo+'&status='+status)
            }
          })


	}
</script>