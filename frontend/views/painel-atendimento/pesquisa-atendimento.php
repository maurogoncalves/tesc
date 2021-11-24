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
		use common\models\Escola;
		use common\models\Condutor;
		use common\models\Aluno;
		use common\models\CondutorRota;
	use  yii\web\Session;
	use kartik\export\ExportMenu;

		$this->title = $titulo;
		$arrayAnos = [];
		$this->title = ' Pesquisa de Atendimento';
		$this->params['breadcrumbs'][] = ['label' => 'Painel de atendimento', 'url' => ['index']];
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="box box-solid">
				
					<div class="box-body">
						<?= Html::beginForm(['painel-atendimento/pesquisa-atendimento'], 'GET', ['id' => 'formFilter']); ?>
						<div class="row form-group">
							<div class="col-md-3">
								<?php
									echo Html::label('Condutores', 'idCondutor');
										echo Select2::widget([
											'name' => 'idCondutor',
											'attribute' => 'idCondutor',
											'data' => $condutores,
												'value' => $_GET['idCondutor'] ? $_GET['idCondutor'] : '',
											'options' => ['placeholder' => 'Selecione o condutor'],
											'pluginOptions' => [
												'allowClear' => true
											],
										]);
								?>
							</div>
							<div class="col-md-3">
							<?php
								echo Html::label('Período de atendimento');
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
						'layout' => "{items}\n{pager}",

						'toolbar' =>  [
							'{export}{toggleData}',
						],
						'dataProvider' => new ArrayDataProvider([
						'allModels' => $historicos,
						'sort' => [
							'defaultOrder' => ['aluno.nome'=>SORT_ASC],
							'attributes' => [
								'aluno.nome',
								'escola.nome' => [
									'asc' => ['escola.nome' => SORT_ASC, 'id' => SORT_ASC],
									'desc' => ['escola.nome' => SORT_DESC, 'id' => SORT_ASC],
									'default' => SORT_ASC
								],
								'aluno.RA',
								'horarioEntrada',
								'horarioSaida',
								'criacao',
								'movimentacaoAssociada.criacao'
							],
						],
						'pagination' => [
							'pageSize' => 20,
						],
						]),
						'columns' => [
							// 'id',
							// 'idHistoricoMovimentacaoAssociado',
							// [
							// 	'attribute' => 'escola.nome',
							// 	'label' => 'Escola',
							// 	'value' =>  function($model){
							// 		return $model->escola->nomeCompleto;
							// 	}
							// ],
							[
								'attribute' => 'aluno.id',
								'label' => 'Id',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model){
									return $model->aluno->id;
								}
							],
							[
								'attribute' => 'aluno.nome',
								'label' => 'Nome',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model){
									return $model->aluno->nome;
								}
							],
							
							[
								'attribute' => 'aluno.RA',
								'label' => 'RA',
								'value' => function($model){
									return $model->aluno->RA.'-'.$model->aluno->RAdigito;
								}
							],
							[
                                'attribute' => 'data',
                                'value' => function ($model) {
                                    return ($model->criacao) ? Yii::$app->formatter->asDate($model->criacao, 'dd/MM/Y') : '';
                                },
                                'filterType' => GridView::FILTER_DATE,
                                'filterWidgetOptions' => [
                                    'pluginOptions' => [
                                        'format' => 'dd/mm/yyyy',
                                        'autoclose' => true,
                                        'todayHighlight' => true,
                                    ]
                                ]
                            ],						
							[
								'attribute' => 'horarioEntrada',
								'label' => 'Horário de Entrada',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model) {
									return $model->aluno->horarioEntrada; 
								}
							],
							[
								'attribute' => 'horarioSaida',
								'label' => 'Horário de Saída',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model) {
									return $model->aluno->horarioSaida;
								}
							],
							[
								'attribute' => 'ano',
								'label' => 'Ano/Série e Turma',
								'contentOptions' => ['style' => 'min-width:60px;'],
								'value' =>   function($model){
									return $model->aluno->turma ? Aluno::ARRAY_SERIES[$model->aluno->serie].'/'.Aluno::ARRAY_TURMA[$model->aluno->turma] : '-';
								},
							],
							[
								'attribute' => 'endereco',
								'label' => 'Endereço',
								'value' =>  function($model) {
									return $model->aluno->tipoLogradouro.' '.$model->aluno->endereco.' Nº '.$model->aluno->numeroResidencia;
								}
							],
							[
								'attribute' => 'bairro',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model) {
									return $model->aluno->bairro;
								}
							],
							[
								'attribute' => 'escola',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model) {	
									return $model->aluno->escola->nome;
								}
							], 							
								// 	'attribute' => 'id',
								// 	'value' => function($model) {
								// 		return $model->aluno->id;
								// 	}
								// ],
								// [
								// 	'attribute' => 'status',
								// 	'value' => function ($model) {
								// 		$data = explode('- ', $_GET['periodo']);		
								// 		$data[0] = explode('/', $data[0]);
								// 		$data[0] = trim($data[0][2]) . '-' . trim($data[0][1]) . '-' . trim($data[0][0]);
								// 		$data[1] = explode('/', $data[1]);
								// 		$data[1] = trim($data[1][2]) . '-' . trim($data[1][1]) . '-' . trim($data[1][0]);

								// 		// return $data[0];
								// 		return  print_r($model->aluno->getStatusNoPeriodo($data[0], $data[1]), true);
								// 	}
								// ],
						],
						'exportConfig' => [
							GridView::HTML => true,
							GridView::CSV => true,
							GridView::TEXT => true,
							GridView::EXCEL => true,
							GridView::PDF => true
						]
					]); ?>
					<?php Pjax::end(); ?>
				</div>
				</div>
			</section>
		</div>

		<script type="text/javascript">
			let condutorName = '';
			let peridoPesquisa = '';

			$(window).on("load", function(){
				getCondutorAndPeriodo();			
			});

			var idCondutor = '<?= isset($get['idCondutor']) ?  $get['idCondutor'] : '' ?>';
			var escola = '<?= isset($get['escola']) ?  $get['escola'] : '' ?>';
			var page = '<?= isset($get['_tog7f728364']) ?  $get['_tog7f728364'] : '' ?>';
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

			function getCondutorAndPeriodo(){
				condutorName = $('span[class=select2-selection__clear]').parent().text().split('×')[1]
				peridoPesquisa = $('input[name="periodo"]').val().split(' - ');

				if(condutorName == undefined)
					condutorName = ''
				
				if(peridoPesquisa == undefined || peridoPesquisa == ''){
					peridoPesquisa[0] = '';
					peridoPesquisa[1] = '';
				}else{
					peridoPesquisa[0] = peridoPesquisa[0].replace("/20","/");
					peridoPesquisa[1] = peridoPesquisa[1].replace("/20","/");
				}


				$('thead').prepend(`<tr >
										<th></th>
										<th style="font-size: 10px;">Período: ${peridoPesquisa[0]} a ${peridoPesquisa[1]}</th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th style="font-size: 10px;">Condutor:</th>
										<th style="font-size: 10px;">${condutorName}</th>
									<tr>`);

			}


			function gerenciadorPdf(){
				let get = '';
				if(idCondutor) {
					get += '&idCondutor='+idCondutor;
				}
				if(escola) {
					get += '&escola='+escola;
				}
				window.open('index.php?r=painel-atendimento/report-pesquisa-atendimento'+get)
			}
			
			setInterval(() => {
			itens = $("#w5 li");
			item = $("#w5 li")[itens.length-1];
			if($(item).prop('title') != 'Portable Document Format') {
						$("#w5").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')

			}
			}, 500);
	</script>
