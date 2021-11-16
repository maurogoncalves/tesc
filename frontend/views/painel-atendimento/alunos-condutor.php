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
use yii\bootstrap\Dropdown;
use  yii\web\Session;

	$this->title = $titulo;
	$arrayAnos = [];
	$this->title = ' Alunos por Condutor';
	$this->params['breadcrumbs'][] = ['label' => 'Painel de atendimento', 'url' => ['index']];

	function mountSelect($camposOrdenacao,$index){
		$str = '<option value="">Selecione</option>';

		$option = '';
		if(isset($_GET['order-'.$index]) && $_GET['order-'.$index] != '')
			$option = $_GET['order-'.$index];
		// exit(1);
		// print $option;
		foreach($camposOrdenacao as $key=>$value){
			$sel = '';
			if($option == $key){
				$sel = ' selected ';
			}
			$str .= '<option value="'.$key.'" '.$sel.' >'.$value.'</option>';
		} 	
	return $str;
	}
?>

	<style>
	.btn-toolbar{
		width:100%;
		text-align: right;	
	}
	</style>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid">

				<div class="box-body">
					<div class="col-md-12">
						<h4>Filtros</h4>
					</div>
					<?= Html::beginForm(['painel-atendimento/alunos-condutor'], 'GET', ['id' => 'formFilter']); ?>
					<?php echo Html::hiddenInput('pageSize',  $_GET['pageSize'] ? $_GET['pageSize'] : ''); ?>
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
					</div>
					<div class="row">
							<br>
							<div class="col-md-12">
								<h4>Ordenação</h4>
							</div>
							<?php for($i = 0; $i <= 9; $i++): ?>
							<div class="col-md-3">
							<?php 
									echo Html::label(($i+1).'ª', 'order-'.$i);
									$options = mountSelect($camposOrdenacao,$i);
									print "
										<select class='form-control' id='order-".$i."' name='order-".$i."' onChange='gerarOpcoesOrdenacao(this.value)' >'
										".$options."
										</select>
									"
									// echo Html::activeDropDownList($camposOrdenacao, '',['1' => 'Yes', '0' => 'No']) ;
										// echo HtmldropDownList::widget([
										// 	'name' => 'order-'.$i,
										// 	'attribute' => 'order-'.$i,
										// 	'data' => $camposOrdenacao,
										// 	'value' => $_GET['order-'.$i] ? $_GET['order-'.$i] : '',
										// 	'options' => [
										// 		'placeholder' => 'Selecione a ordenação',
										// 		'id' => 'order-'.$i
										// 	],
										// 	'pluginOptions' => [
										// 		'allowClear' => true
										// 	],
										// 	'pluginEvents' => [
										// 		"change" => "function() { gerarOpcoesOrdenacao(this.value) }",
										// 	  ],
										// ]);
								?>
							</div>
							<?php endfor; ?>
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
                    <h4>
                    <?= '<span class="label label-primary">Total: '.count($alunos).'</span>'; ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
	<div class="row">
		<section class="col-md-12"> 
			<div class="box box-solid">
			<div class="box-body">
				<?php Pjax::begin();
				function createOption($val){
					$selected = isset($_GET['pageSize']) && $val == $_GET['pageSize'] ? ' selected ' : '';
					return '<option value="'.$val.'" '.$selected.' >'.$val.'</option>';
				}
				$toolbar = [];
				$select = '<div class="pull-left">Mostrar';
				$select .= '<select class="" name="" id="paginacao">';
				$select .= createOption(200);
				$select .= createOption(500);
				$select .= createOption(1000);
				$select .= createOption(5000);
				$select .= createOption(10000);
				$select .= '</select>';
				$select .= 'registros.</div><div class="pull-right">';
				
				// $toolbar[] =  Html::button('<i class="glyphicon glyphicon-export"></i>', ['value' => Url::to(['painel-atendimento/gerenciar-exportacao', 'selecionados' => '']), 'title' => 'Nova Solicitação', 'class' => 'showModalButton btn btn-default']);

				$select .= Html::a('<i class="glyphicon glyphicon-export"></i>', ['#'], ['class' => 'btn btn-default','onclick'=>"gerenciarExportacao(event)"]);

				$toolbar[]='';
				$toolbar[]='</div>';	
				$toolbar[] = $select;
				?>
				<?= GridView::widget([
					
					'panel' => [
						'heading'=>false,
						'type'=>false,
						'showFooter'=>false
					],
					'toolbar' =>  $toolbar,
					'dataProvider' => new ArrayDataProvider([
					
					'allModels' => $alunos,
					'sort' => [
						'attributes' => [
							'nome',
							 'escola.nome' => [
				                'asc' => ['escola.nome' => SORT_ASC, 'id' => SORT_ASC],
				                'desc' => ['escola.nome' => SORT_DESC, 'id' => SORT_ASC],
				                'default' => SORT_ASC
				            ],
							'RA',
							'horarioEntrada',
							'horarioSaida',
						],
					],
					'pagination' => [
						'pageSize' => $pageSize,
					],
					]),
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
						[
							'attribute' => 'nome',
							'label' => 'Nome',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model) {
								return $model->nome;
							}
						],
						[
							'attribute' => 'escola.nome',
							'label' => 'Escola',
							'value' =>  function($model){
								return $model->escola->nomeCompleto;
							}
						],
						[
							'attribute' => 'RA',
							'label' => 'RA',
							'value' => function($model){
								return $model->RA.'-'.$model->RAdigito;
							}
						],
						[
							'attribute' => 'horarioEntrada',
							'label' => 'Horário de Entrada',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model) {
								return $model->horarioEntrada;
							}
						],
						[
							'attribute' => 'horarioSaida',
							'label' => 'Horário de Saída',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model) {
								return $model->horarioSaida;
							}
						],
						[
							'attribute' => 'ano',
							'label' => 'Ano/Série e Turma',
							'contentOptions' => ['style' => 'min-width:60px;'],
							'value' =>   function($model){
								return $model->turma ? Aluno::ARRAY_SERIES[$model->serie].'/'.Aluno::ARRAY_TURMA[$model->turma] : '-';
							},
						],
						[
							'attribute' => 'telefoneResidencial',
							'label' => 'Tel.',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model) {
								if($model->telefoneCelular && strlen($model->telefoneCelular) > 5)
									 return $model->telefoneCelular;
								if($model->telefoneCelular2 && strlen($model->telefoneCelular2) > 5)
									 return $model->telefoneCelular2;
								if($model->telefoneResidencial && strlen($model->telefoneResidencial) > 5)
									 return $model->telefoneResidencial;
								if($model->telefoneResidencial2 && strlen($model->telefoneResidencial2) > 5)
									 return $model->telefoneResidencial2;

								return '-';
							}

						],
						[
							'attribute' => 'endereco',
							'label' => 'Endereço',
							'value' =>  function($model) {
								return $model->tipoLogradouro.' '.$model->endereco.' Nº '.$model->numeroResidencia;
							}
						],
						'complementoResidencia',
						[
							'attribute' => 'bairro',
							'contentOptions' => ['style' => 'min-width:80px;'],

						], 
						[
							'label' => 'Necessidades', 
							'contentOptions' => ['style' => 'min-width:120px;'],
							'value' => function($model){
								$necessidades = $model->necessidades;
								$redes = [];
							foreach ($necessidades as $necessidade)
							{
								$redes[] = $necessidade->necessidadesEspeciais->nome;
							}

							return implode (', ', $redes);
							}
						],
						[
							'label' => 'Início',
							'value' => function($model){
								$data = $model->solicitacaoAtiva->atendimento->dataCadastro;
								return ($model && $data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-';
							}
						],
						[
							'label' => 'Condutor Entrada',
							'value' => function($model){
								return $model->solicitacaoAtiva ? $model->solicitacaoAtiva->rotaIda->condutor->nome : '-';
							}
						],
						[
							'label' => 'Tel.',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model){
								if($model->solicitacaoAtiva)
								{
									return Yii::$app->formatter->asTelefone($model->solicitacaoAtiva->rotaIda->condutor->telefone);
									// return $model->solicitacaoAtiva->rotaIda->condutor->telefone;
								}
								return '-'; 
							}
						],
						[
							'label' => 'Alvará',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model){
								if($model->solicitacaoAtiva)
								{
									return $model->solicitacaoAtiva->rotaIda->condutor->alvara;
								}
								return '-';
							}
						],
						[
							'label' => 'Condutor Saída',
							'value' => function($model){
								return $model->solicitacaoAtiva ? $model->solicitacaoAtiva->rotaVolta->condutor->nome : '-';
							}
						],
						[
							'label' => 'Tel.',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model){
								if($model->solicitacaoAtiva)
								{
									return $model->solicitacaoAtiva->rotaVolta->condutor->telefone;
								}
								return '-';
							}
						],
						[
							'label' => 'Alvará',
							'contentOptions' => ['style' => 'min-width:80px;'],
							'value' => function($model){
								if($model->solicitacaoAtiva)
								{
									return $model->solicitacaoAtiva->rotaVolta->condutor->alvara;
								}
								return '-';
							}
						],
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
		var ordenacoes = [];
		ordenacoes.push({value: 'Escola.nome A-Z' , label: 'Escola - A-Z'});
		ordenacoes.push({value: 'Escola.nome Z-A' , label: 'Escola - Z-A'});
		ordenacoes.push({value: 'Aluno.nome Z-A' , label: 'Nome - Z-A'});
		ordenacoes.push({value: 'Aluno.nome A-Z' , label: 'Nome - A-Z'});
		ordenacoes.push({value: 'Aluno.horarioEntrada A-Z' , label: 'Horário de entrada - A-Z'});
		ordenacoes.push({value: 'Aluno.horarioEntrada Z-A' , label: 'Horário de entrada - Z-A'});
		ordenacoes.push({value: 'Aluno.horarioSaida A-Z' , label: 'Horário de saída - A-Z'});
		ordenacoes.push({value: 'Aluno.horarioSaida Z-A' , label: 'Horário de saída - Z-A'});
		ordenacoes.push({value: 'Aluno.RA A-Z' , label: 'RA - A-Z'});
		ordenacoes.push({value: 'Aluno.RA Z-A' , label: 'RA - Z-A'});
		ordenacoes.push({value: 'Aluno.endereco A-Z' , label: 'Endereço - A-Z'});
		ordenacoes.push({value: 'Aluno.endereco Z-A' , label: 'Endereço - Z-A'});
		ordenacoes.push({value: 'Aluno.bairro A-Z' , label: 'Bairro - A-Z'});
		ordenacoes.push({value: 'Aluno.bairro Z-A' , label: 'Bairro - Z-A'});
		ordenacoes.push({value: 'Aluno.serie A-Z' , label: 'Ano/Série - A-Z'});
		ordenacoes.push({value: 'Aluno.serie Z-A' , label: 'Ano/Série - Z-A'});
		ordenacoes.push({value: 'Aluno.turma A-Z' , label: 'Turma - A-Z'});
		ordenacoes.push({value: 'Aluno.turma Z-A' , label: 'Turma - Z-A'});
		ordenacoes.push({value: 'CondutorEntrada.nome A-Z' , label: 'Condutor Entrada - A-Z'});
		ordenacoes.push({value: 'CondutorEntrada.nome Z-A' , label: 'Condutor Entrada - Z-A'});
		ordenacoes.push({value: 'CondutorEntrada.alvara A-Z' , label: 'Alvará Entrada - A-Z'});
		ordenacoes.push({value: 'CondutorEntrada.alvara Z-A' , label: 'Alvará Entrada - Z-A'});
		ordenacoes.push({value: 'CondutorSaida.nome A-Z' , label: 'Condutor Saída - A-Z'});
		ordenacoes.push({value: 'CondutorSaida.nome Z-A' , label: 'Condutor Saída - Z-A'});
		ordenacoes.push({value: 'CondutorSaida.alvara A-Z' , label: 'Alvará Saída - A-Z'});
		ordenacoes.push({value: 'CondutorSaida.alvara Z-A' , label: 'Alvará Saída - Z-A'});
		ordenacoes.push({value: 'SolicitacaoStatus.dataCadastro A-Z' , label: 'Início - A-Z'});
		ordenacoes.push({value: 'SolicitacaoStatus.dataCadastro Z-A' , label: 'Início - Z-A'});
		ordenacoes.push({value: 'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) A-Z' , label: 'Necessidade Especial COM-SEM'});
		ordenacoes.push({value: 'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) Z-A' , label: 'Necessidade Especial SEM-COM'});
			
	function gerarOpcoesOrdenacao (value){
	
		var ordenacaoClone = JSON.parse(JSON.stringify(ordenacoes))
		var camposSelecionados = []
		for(let inputOrder = 0 ; inputOrder < 10; inputOrder++){
			let campo = $('#order-'+inputOrder).val();
			camposSelecionados.push(campo)
		}

		var listaLimpa = [];
		for(let i = 0 ; i < ordenacaoClone.length; i++){
			let campo = ordenacaoClone[i]
			if(!camposSelecionados.includes(campo.value)){
				listaLimpa.push(campo)
			}
		}
		montarCampos(listaLimpa, value)
		console.log(listaLimpa)
	}
	
	function montarCampos(arrayCampos, value=false){
		for(let inputOrder = 0 ; inputOrder < 10; inputOrder++){

			let esteCampo = $('#order-'+inputOrder).val();
			let esteCampo2 = $('#order-'+inputOrder+' option:selected').text();

			$('#order-'+inputOrder).empty()
			$('#order-'+inputOrder).prepend(`<option value='' selected>Selecione</option>`);

			console.warn(value ,value != true)
			if(esteCampo2 != 'Selecione'){
				$('#order-'+inputOrder).append(`<option value="${esteCampo}" selected >${esteCampo2}</option>`);
			} 
			// console.warn(value);	
			for(let campo = 0; campo < arrayCampos.length; campo++){
				let opcao = arrayCampos[campo];
				$('#order-'+inputOrder).append(`<option value="${opcao.value}">${opcao.label}</option>`);
			}
		}
	}



	// A $( document ).ready() block.
	$( document ).ready(function() {
		setTimeout(() => gerarOpcoesOrdenacao(1), 500)
	});
	
		// ' => ,
        // '' => '',
        // '' => '',
        // '' => '',
        // 'Aluno.horarioEntrada A-Z' => 'Horário de entrada - A-Z',
        // 'Aluno.horarioEntrada Z-A' => 'Horário de entrada - Z-A',
        // 'Aluno.horarioSaida A-Z' => 'Horário de saída - A-Z',
        // 'Aluno.horarioSaida Z-A' => 'Horário de saída - Z-A',
        // 'Aluno.horarioSaida A-Z' => 'Horário de saída - A-Z',
        // 'Aluno.horarioSaida Z-A' => 'Horário de saída - Z-A',
        // 'Aluno.RA A-Z' => 'RA - A-Z',
        // 'Aluno.RA Z-A' => 'RA - Z-A',
        // 'Aluno.endereco A-Z' => 'Endereço - A-Z',
        // 'Aluno.endereco Z-A' => 'Endereço - Z-A',
        // 'Aluno.bairro A-Z' => 'Bairro - A-Z',
        // 'Aluno.bairro Z-A' => 'Bairro - Z-A',
        // 'Aluno.serie A-Z' => 'Ano/Série - A-Z',
        // 'Aluno.serie Z-A' => 'Ano/Série - Z-A',
        // 'Aluno.turma A-Z' => 'Turma - A-Z',
        // 'Aluno.turma Z-A' => 'Turma - Z-A',
        // 'CondutorEntrada.nome A-Z' => 'Condutor Entrada - A-Z',
        // 'CondutorEntrada.nome Z-A' => 'Condutor Entrada - Z-A',
        // 'CondutorSaida.nome A-Z' => 'Condutor Saída - A-Z',
        // 'CondutorSaida.nome Z-A' => 'Condutor Saída - Z-A',
        // 'SolicitacaoStatus.dataCadastro A-Z' => 'Início - A-Z',
        // 'SolicitacaoStatus.dataCadastro Z-A' => 'Início - Z-A',
        // 'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) A-Z' => 'Necessidade Especial COM-SEM',
        // 'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) Z-A' => 'Necessidade Especial SEM-COM',
	
	var idCondutor = '<?= isset($get['idCondutor']) ?  $get['idCondutor'] : '' ?>';
	var escola = '<?= isset($get['escola']) ?  $get['escola'] : '' ?>';
	var sort = '<?= isset($get['sort']) ?  $get['sort'] : '' ?>';
	var order = '<?= '&order-0='.$get['order-0'].'&order-1='.$get['order-1'].'&order-2='.$get['order-2'].'&order-3='.$get['order-3'].'&order-4='.$get['order-4'].'&order-5='.$get['order-5'].'&order-6='.$get['order-6'].'&order-7='.$get['order-7'].'&order-8='.$get['order-8'].'&order-9='.$get['order-9']; ?>';
	var page = '<?= isset($get['_tog7f728364']) ?  $get['_tog7f728364'] : '' ?>';
	var get = '';

	function mountGet(){
		get = '';
		if(idCondutor) {
			get += '&idCondutor='+idCondutor;
		}
		if(escola) {
			get += '&escola='+escola;
		}
		if(pageSize) {
			get += '&pageSize='+pageSize;
		}
		if(sort) {
			get += '&sort='+sort;
		}
		if(order) {
			get += order;
		}
		return get;
	}

	function gerenciarExportacao(event){
		event.preventDefault();
		console.log(1);
		let indexes = [];
		let keys = $('#w2').yiiGridView('getSelectedRows');
		$('.kv-row-checkbox').each(function(index, item) {
			if(keys.includes(index)){
				indexes.push(item)
			}
		});
		
		let checkboxes = [];
		for(let i = 0 ; i < indexes.length; i++){
			checkboxes.push(indexes[i].value);
		}

		let x='todos os registros desta página?';
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
			  window.open('index.php?r=painel-atendimento/gerenciar-exportacao'+mountGet()+'&selecionados='+checkboxes)
            }
          })


	}


	// console.log(indexes);
	
	// setInterval(() => {
	// 	var keys = $('#w2').yiiGridView('getSelectedRows');
	// 	console.log(keys);
	// }, 500)

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
			window.open('index.php?r=painel-atendimento/report'+mountGet())
		}
		
	// 	setInterval(() => {
	// itens = $("#w4 li");-
	// 	item = $("#w4 li")[itens.length-1];
	// 	if($(item).prop('title') != 'Portable Document Format') {
	// 				$("#w4").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')

	// 	}
	// 	}, 500);

		// $("#paginacao").change(() => {
		// 	pageSize = $("#paginacao").val();
		// 	// $('input[name="pageSize"]').value(pageSize);
		// 	window.location.href = 'index.php?r=painel-atendimento/alunos-condutor'+mountGet()

		// });

	</script>
