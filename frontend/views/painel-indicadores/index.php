<?php
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use common\models\SolicitacaoTransporte;
use common\models\SolicitacaoCredito;
use common\models\Escola;
use common\models\Condutor;
use common\models\ReciboPagamentoAutonomo;
 
$this->title = 'Valor transferido em Passe Escolar';
$this->params['breadcrumbs'][] = ['label' => 'Painel de indicadores', 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
$arrayAnos = [];

?>
<div class="btn-group pull-right" style="display: none;">
	<button id="w55" class="btn btn-default dropdown-toggle " title="Exportar" data-toggle="dropdown" aria-expanded="false" style="color:#3980D8">Exportar dados  <i class="glyphicon glyphicon-cloud-download"></i>  </button>
	<ul id="w66" class="dropdown-menu dropdown-menu-right">
		<li title="Texto Delimitado por Tabulação"><a class="export-txt" onclick='gerenciarExportacao(event,"TXT")' data-mime="text/plain" data-hash="b7d45805ba6739212bd208b8d5896e0ccaad77368c9df96b5dd483320ddecb67gridviewexportar-listagemtext/plainutf-81{&quot;colDelimiter&quot;:&quot;\t&quot;,&quot;rowDelimiter&quot;:&quot;\r\n&quot;}" data-css-styles="[]" tabindex="-1"><i class="text-muted glyphicon glyphicon-floppy-save"></i> Texto</a></li>
		<li title="Microsoft Excel 95+"><a class="export-xls" onclick='gerenciarExportacao(event,"EXCEL")' data-mime="application/vnd.ms-excel" data-hash="c78def80d35ad515b4ececb6260d2a82230d11149b73a853a3f74d8ea62c7dfcgridviewexportar-listagemapplication/vnd.ms-excelutf-81{&quot;worksheet&quot;:&quot;ExportarPlanilha&quot;,&quot;cssFile&quot;:&quot;&quot;}" data-css-styles="{&quot;.kv-group-even&quot;:{&quot;background-color&quot;:&quot;#f0f1ff&quot;},&quot;.kv-group-odd&quot;:{&quot;background-color&quot;:&quot;#f9fcff&quot;},&quot;.kv-grouped-row&quot;:{&quot;background-color&quot;:&quot;#fff0f5&quot;,&quot;font-size&quot;:&quot;1.3em&quot;,&quot;padding&quot;:&quot;10px&quot;},&quot;.kv-table-caption&quot;:{&quot;border&quot;:&quot;1px solid #ddd&quot;,&quot;border-bottom&quot;:&quot;none&quot;,&quot;font-size&quot;:&quot;1.5em&quot;,&quot;padding&quot;:&quot;8px&quot;},&quot;.kv-table-footer&quot;:{&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-page-summary td&quot;:{&quot;background-color&quot;:&quot;#ffeeba&quot;,&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-align-center&quot;:{&quot;text-align&quot;:&quot;center&quot;},&quot;.kv-align-left&quot;:{&quot;text-align&quot;:&quot;left&quot;},&quot;.kv-align-right&quot;:{&quot;text-align&quot;:&quot;right&quot;},&quot;.kv-align-top&quot;:{&quot;vertical-align&quot;:&quot;top&quot;},&quot;.kv-align-bottom&quot;:{&quot;vertical-align&quot;:&quot;bottom&quot;},&quot;.kv-align-middle&quot;:{&quot;vertical-align&quot;:&quot;middle&quot;},&quot;.kv-editable-link&quot;:{&quot;color&quot;:&quot;#428bca&quot;,&quot;text-decoration&quot;:&quot;none&quot;,&quot;background&quot;:&quot;none&quot;,&quot;border&quot;:&quot;none&quot;,&quot;border-bottom&quot;:&quot;1px dashed&quot;,&quot;margin&quot;:&quot;0&quot;,&quot;padding&quot;:&quot;2px 1px&quot;}}" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>
		<li title="Portable Document Format"><a class="export-pdf"  tabindex="-1"  onclick='gerenciarExportacao(event,"PDF")'><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
		
			<div class="box-body">
			    <?= Html::beginForm(['painel-indicadores/index'], 'GET', ['id' => 'formFilter']); ?>
			    <div class="row form-group">
				    <div class="col-md-3">
					    <?php
					    	echo Html::label('Período', 'periodo');
					    	echo DateRangePicker::widget([
							    'name' => 'periodo',
							    'value' => isset($_GET['periodo'])?$_GET['periodo']:'',
							    'attribute'=>'datetime_range',
							    'convertFormat'=>true,
							    'pluginOptions'=>[
							        'timePicker'=>false,
							        'timePickerIncrement'=>30,
							        'locale'=>[
							            'format'=>'d/m/Y'
							        ]
							    ],
							    'options' => [
							    	'autocomplete' => 'off',
							    	'class' => 'form-control'
							    ] 
							]);
					    ?>
				    </div>

						<div class="col-md-3">
					    <?php
					    	echo Html::label('Escola', 'escola');
								echo Select2::widget([
									'name' => 'escola',
							    'attribute' => 'idEscola',
							    'data' => $escolas,
									'value' => isset($_GET['escola']) ? $_GET['escola'] : '',
							    'options' => ['placeholder' => 'Selecione as escolas'],
							    'pluginOptions' => [
							        'allowClear' => true
							    ],
								]);
					    ?>
				    </div>

						<div class="col-md-3">
					    <?php
					    	echo Html::label('Unidade', 'unidade');
								echo Select2::widget([
									'name' => 'unidade',
							    'attribute' => 'unidade',
									'value' => isset($_GET['unidade']) ? $_GET['unidade'] : '',
							    'data' =>  Escola::ARRAY_UNIDADE,
							    'options' => ['placeholder' => 'Selecione a unidade'],
							    'pluginOptions' => [
							        'allowClear' => true
							    ],
								]);
					    ?>
				    </div>
				</div> 

			    <div class="row">
				    <div class="col-md-12">
				    	<?= Html::submitButton('Filtrar', ['class' => 'btn btn-primary pull-right']) ?>
					
				    </div>
				</div>

			    <?php echo Html::endForm(); ?>
			</div>
		</div>
	</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 style="text-align:center">
						<span class="label label-primary">Valor Total Transferido: R$ <?= \Yii::$app->formatter::DoubletoReal($total) ?></span>&nbsp;
						<span class="label label-info">Quantidade de Solicitações:  <?= count($solicitacoes) ?></span>
					</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
	<section class="col-md-12">
		<div class="box box-solid">
		  <div class="box-body">
		    <?php Pjax::begin(); ?>
		      <?= GridView::widget([
				'panel' => [
					'heading'=>false,
					'type'=>false,
					'showFooter'=>false
				],
				'toolbar' => \Yii::$app->showEntriesToolbar->create(),
		        'dataProvider' => new ArrayDataProvider([
		          'allModels' => $solicitacoes,
		          'sort' => [ 
		              'attributes' => [
		                // 'idEscola',
		                // 'status',
		                // 'inicio',
		                // 'fim',
		                // 'criado',
		                // 'creditoAdministrativo',
		                // 'total'
		              ],
		          ], 
		          'pagination' => [
					'pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20,
		          ],
		        ]),
		        'columns' => [
					'id',
					[
						'attribute' => 'tipoSolicitacao',
						'value' => function($model) {
							return $model->tipoSolicitacao ? SolicitacaoCredito::TIPO[$model->tipoSolicitacao] : '-';
						},
						'filterType' => GridView::FILTER_SELECT2,
						'filter' =>  SolicitacaoCredito::TIPO, 
						'filterWidgetOptions' => [
							'pluginOptions' => ['allowClear' => true],
						],
						'filterInputOptions' => [
							'placeholder' => '-',
						]
					],
					[
						'attribute' => 'idEscola',
						'value' => function($model){
							return $model->escola->nome;//Yii::t('app', $model->escola->nome);
						},
						'filterType' => GridView::FILTER_SELECT2,
						'filter' => ArrayHelper::map(Escola::escolasPerfis($model->escola), 'id', 'nome'), 
						'filterWidgetOptions' => [
							'pluginOptions' => ['allowClear' => true],
						],
						'filterInputOptions' => [
							'placeholder' => '-',
							
						]
					],
					[
						'attribute' => 'mesInicio',
						'value' => function($model) {
							return $model->mesInicio ? ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesInicio] : '-';
						},
						'filterType' => GridView::FILTER_SELECT2,
						'filter' => ReciboPagamentoAutonomo::ARRAY_MESES, 
						'filterWidgetOptions' => [
							'pluginOptions' => ['allowClear' => true],
						],
						'filterInputOptions' => [
							'placeholder' => '-',
						]
					],
					[
						'attribute' => 'mesFim',
						'value' => function($model) {
							return $model->mesFim ? ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesFim] : '-';
						},
						'filterType' => GridView::FILTER_SELECT2,
						'filter' =>  ReciboPagamentoAutonomo::ARRAY_MESES, 
						'filterWidgetOptions' => [
							'pluginOptions' => ['allowClear' => true],
						],
						'filterInputOptions' => [
							'placeholder' => '-',
						]
					],
		          [
		            'attribute' => 'valorTransferido',
		            'label' => 'Valor Transferido',
		            'value' => function ($model) {
					return $model->valorTransferido ? \Yii::$app->formatter::DoubletoReal($model->valorTransferido) : '-';
						//   if ($model->solicitacaoCreditoAlunos) {
		            //     $valor = 0;
		            //     foreach ($model->solicitacaoCreditoAlunos as $key => $value) {
		            //       $valor += $value->valor;
		            //     }
		            //     return $valor;
		            //   } else {
		            //     return 0;
		            //   }
		            }
				],
				  [
		            'attribute' => 'dataTransferencia',
		            'label' => 'Data da transferência',
		            'value' => function ($model) { 
						//date("d/m/Y", strtotime($model->dataTransferencia))
					return $model->dataTransferencia ?  date("d/m/Y", strtotime($model->dataTransferencia)) : '-';
						//   if ($model->solicitacaoCreditoAlunos) {
		            //     $valor = 0;
		            //     foreach ($model->solicitacaoCreditoAlunos as $key => $value) {
		            //       $valor += $value->valor;
		            //     }
		            //     return $valor;
		            //   } else {
		            //     return 0;
		            //   }
		            }
				],
				
				],
				'exportConfig' => [
					GridView::HTML => false,
					GridView::CSV => false,
					GridView::TEXT => false,
					GridView::EXCEL => false,
					GridView::PDF=> false,
				]
			]); ?>
		    <?php Pjax::end(); ?>
		  </div>
		</div>
	</section>
</div>

<script type="text/javascript">
	$( "#mesFim").change(function () {
		if ($(this).val() < $("#mesInicio").val())
		{
			alert('O mês final deve ser maior ou igual ao mês inicial');
			$(this).val('12');
		}
	})

	$( "#mesInicio").change(function () {
		if ($(this).val() > $("#mesFim").val())
		{
			alert('O mês inicial deve ser menor ou igual ao mês final');
			$(this).val('12');
		}
	})

	function gerenciarExportacao(event, tipo){
		event.preventDefault();
		
		Swal.fire({
            title: 'Exportar registros',
            text: "Confirma a exportação?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO'
          }).then((result) => {
            if (result.value) {
			  window.open('index.php?r=painel-indicadores/export-painel-indicadores&tipo='+tipo)
            }
          })


	}
setTimeout(() => {
    document.getElementById('w5').innerHTML= document.getElementById('w66').innerHTML
}, 200)
$(document).ready(function() {
setTimeout(() => $("#w5").html($("#w66").html()), 200)

    
});
</script>
