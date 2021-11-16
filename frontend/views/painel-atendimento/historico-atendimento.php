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
		$this->title = ' Histórico de Atendimento';
		$this->params['breadcrumbs'][] = ['label' => 'Painel de atendimento', 'url' => ['index']];
		?>
		<div class="row">
		<?php  
	// 	  echo '<ul>' . ExportMenu::widget([
	//     'dataProvider' => $dt,
	//     'columns' => $gc,
	//     'asDropdown' => false
	// ]) . '</ul>';
		?>
			<div class="col-md-12">
				<div class="box box-solid">
				
					<div class="box-body">
						<?= Html::beginForm(['painel-atendimento/historico-atendimento'], 'GET', ['id' => 'formFilter']); ?>
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
								echo Html::label('Período de início', 'periodo_inicio');
								echo DateRangePicker::widget([
									'name' => 'periodo_inicio',
									'value' => isset($_GET['periodo_inicio'])?$_GET['periodo_inicio']:'',
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
								echo Html::label('Período de fim', 'periodo_fim');
								echo DateRangePicker::widget([
									'name' => 'periodo_fim',
									'value' => isset($_GET['periodo_fim'])?$_GET['periodo_fim']:'',
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
							],
						],
						'pagination' => [
							'pageSize' => 10,
						],
						]),
						'columns' => [
							// 'id',

							[
								'attribute' => 'escola.nome',
								'label' => 'Escola',
								'value' =>  function($model){
									return $model->escola->nomeCompleto;
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
								'attribute' => 'horarioEntrada',
								'label' => 'Horário de Entrada',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model) {
									return $model->entrada; 
								}
							],
							[
								'attribute' => 'horarioSaida',
								'label' => 'Horário de Saída',
								'contentOptions' => ['style' => 'min-width:80px;'],
								'value' => function($model) {
									return $model->saida;
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
								'label' => 'Necessidades', 
								'contentOptions' => ['style' => 'min-width:120px;'],
								'value' => function($model){
									$necessidades = $model->aluno->necessidades;
									$redes = [];
								foreach ($necessidades as $necessidade)
								{
									$redes[] = $necessidade->necessidadesEspeciais->nome;
								}

								return implode (', ', $redes);
								}
							],
							// [
							// 	'attribute' => 'sentido',
							// 	'label' => 'Sentido',
								
							// 	'value'=>  function($model){
							// 		return $model->sentido ? CondutorRota::ARRAY_SENTIDO_RESUMIDO[$model->sentido] : '-';
							// 	},
							// ],
							[
								'attribute' => 'criacao',
								'label' => 'Início do atendimento',
								'value' => function($model) {
									return $model->inicioAtendimento;
								}, 
							],
							[
								'attribute' => 'movimentacaoAssociada.criacao',
								'label' => 'Fim do atendimento',
								'value' => function($model) {
									return $model->fimAtendimento;
								},
							]
						],
						'exportConfig' => [
							GridView::HTML => true,
							GridView::CSV => true,
							GridView::TEXT => true,
							GridView::EXCEL => true
						]
					]); ?>
					<?php Pjax::end(); ?>
				</div>
				</div>
			</section>
		</div>

		<script type="text/javascript">
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


		function gerenciadorPdf(){
			let get = '';
			if(idCondutor) {
				get += '&idCondutor='+idCondutor;
			}
			if(escola) {
				get += '&escola='+escola;
			}
			window.open('index.php?r=painel-atendimento/report-historico-atendimento'+get)
		}
		
		setInterval(() => {
		itens = $("#w5 li");
		item = $("#w5 li")[itens.length-1];
		if($(item).prop('title') != 'Portable Document Format') {
					$("#w5").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')

		}
		}, 500);



	</script>
