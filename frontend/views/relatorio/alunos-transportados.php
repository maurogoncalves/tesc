<?php
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use common\models\Escola;
use common\models\Condutor;

$this->title = $titulo;
$arrayAnos = [];

// if (!$data)
// {
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
			<div class="box-header with-border">
				<h4>Filtros</h4>
			</div>
			<div class="box-body">
			    <?= Html::beginForm(['relatorio/alunos-transportados'], 'GET', ['id' => 'formFilter']); ?>
			    <div class="row form-group">
				    <div class="col-md-3">
					    <?php
					    	echo Html::label('Período', 'periodo');
					    	echo DateRangePicker::widget([
							    // 'model'=>$model,
							    'name' => 'periodo',
							    'value' => isset($get['periodo'])?$get['periodo']:'',
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
					    	echo Html::dropdownList('escola', '', ArrayHelper::map(Escola::find()->orderBy('nome ASC')->all(), 'id', 'nome'),['id' => 'escola', 'prompt' => 'Selecione', 'class' => 'form-control']);
					    ?>
				    </div>

				    <div class="col-md-3">
					    <?php
					    	echo Html::label('Condutor', 'condutor');
					    	echo Html::dropdownList('condutor', '', ArrayHelper::map(Condutor::find()->orderBy('nome ASC')->all(), 'id', 'nome'),['id' => 'escola', 'prompt' => 'Selecione', 'class' => 'form-control']);
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
<?php
// }
// else
// {
?>

<!-- <section class="invoice">
	<div class="row">
		<div class="col-xs-12">
		    <div class="btnPrint pull-right">
		        <button class="btn btn-primary" onclick="window.print();return false;">Imprimir</button>
	      		<button type="button" class="btn btn-primary" onclick="sendPDF('<?= Url::to(['relatorio/alunos-transportados', 'pdf' => 1]) ?>','section.relatorio', 'AlunosTransportados.pdf');">Gerar PDF</button>
	      		<button onclick="divs2Excel(['.relatorio'],{filename: 'AlunosTransportados.xls',escape: 'false',htmlContent: 'false'});" class="btn btn-primary">Gerar Excel</button>
		    </div>
		</div>
	</div>
</section> --> 

<div class="row">
	<section class="col-md-12">
		<div class="box box-solid">
			<div class="box-header with-border">
				<h4><?= $this->title ?></h4>
			</div>

			<div class="box-body">
	            <?php Pjax::begin(); ?>    <?= GridView::widget([
					'panel' => [
						'heading'=>false,
						'type'=>false,
						'showFooter'=>false
					],
					'toolbar' =>  [
						'{export}{toggleData}',
					],
	                'dataProvider' => new ArrayDataProvider([
					    'allModels' => $data,
					    'sort' => [
					        'attributes' => ['nome', 'idEscola', 'email'],
					    ],
					    'pagination' => [
					        'pageSize' => 10,
					    ],
					]),
	                // 'filterModel' => $searchModel,
	                // 'showPageSummary' => true,
	                'columns' => [
	                // ['class' => 'yii\grid\SerialColumn'],
	                // 'id',
	                // 'aluno.nome',
	                //  'dataNascimento',
	                //  'nomeMae',
	                //  'nomePai',
	                [
	                    'attribute' => 'RA',
	                    'label' => 'RA',
	                    'value' => function($model){
	                        return $model->aluno->RA.' '.$model->aluno->RAdigito;
	                    }
	                ],
	                [
	                	'attribute' => 'idEscola',
	                	'value' => 'escola.nome',
	                ],
	                // [
	                //     'attribute' => 'idEscola',
	                //     'value' => function($model){
	                //         return $model->escola->id;//Yii::t('app', $model->escola->nome);
	                //     },
	                //     // 'filterType' => GridView::FILTER_SELECT2,
	                //     'filter' => false,
	                // ],
	                // 'cartaoPasse',
	                // 'horarioEntrada',
	                // 'horarioSaida',
	                // 'distanceEscola',
	                // 'barreiraFisica',
	                // 'idRgAluno',
	                // 'idComprovanteEndereco',
	                // 'idRgResponsavel',
	                // 'idDeclaracaoVizinhos',
	                // 'idLaudoMedico',
	                // 'idTransporteEspecialAdaptado',
	                // 'idDeclaracaoInexistenciaVaga',
	                // 'telefoneResidencial',
	                // 'telefoneResidencial2',
	                // 'telefoneCelular',
	                // 'telefoneCelular2',

					// ['class' => 'yii\grid\ActionColumn'],
	                ],
					'exportConfig' => [
						GridView::HTML => true,
						GridView::CSV => true,
						GridView::TEXT => true,
						GridView::EXCEL => true,
						GridView::PDF=> [
							'config' => [
								'mode' => 'c',
								'format' => 'A4-L',
								'destination' => 'D',
								'marginTop' => 50,
								'marginBottom' => 20,
								'marginLeft' => 5,
								'marginRight' => 5,
								'cssInline' => 
									'.img {float:right !important;}'.
									'.table{font-size:10px}' .
									'.kv-wrap{padding:20px;}' .
									'.kv-align-center{text-align:center;}' .
									'.kv-align-left{text-align:left;}' .
									'.kv-align-right{text-align:right;}' .
									'.kv-align-top{vertical-align:top!important;}' .
									'.kv-align-bottom{vertical-align:bottom!important;}' .
									'.kv-align-middle{vertical-align:middle!important;}' .
									'.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
									'.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
									'.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}'.
									'.texto{text-align:left!important;}',
								'methods' => [
									'SetHeader' => '
									<table width="100%">
										<tr>
											<Td align="center">
												<img src="img/brasaoFull.png">
											</Td>
										</tr>
									</table>
									',
									'SetFooter' => [
										['odd' => $pdfFooter, 'even' => $pdfFooter]
									],
								],
								'options' => [
									'title' => $title,
									'subject' => 'xx1',
									'keywords' => 'xx3',
								],
								'contentBefore'=>'',
								'contentAfter'=>''
							]
						],
					]
				]); ?>
	            <?php Pjax::end(); ?>
	        </div>
		</div>
	</section>
</div>

<?php //} ?>

<script type="text/javascript">
	$(document).ready(function() {
	 //    $('#relatorio').DataTable({
	 //    	"paging":   false,
	 //        "ordering": true,
	 //        "order": [[ 1, "desc" ]],
	 //        "info":     false,
	 //        "bFilter": false
	 //    });

		// $('#relatorio_wrapper').doubleScroll({resetOnWindowResize: true});
	/* Defaults */
	});

	// $('#formFilter').submit(function() {
	// 	if (!$( "#inscricaoEstadual").val() && !$( "#cnpj").val() && !$( "#contribuinte").val())
	// 	{
	// 		alert('Informe alguma informação sobre o contribuinte.');
	// 		return false;
	// 	}

	// 	if (!$( "#exercicio").val())
	// 	{
	// 		alert('Informe o ano de exercício.');
	// 		return false
	// 	}

	//     return true; // return false to cancel form action
	// });


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

</script>
