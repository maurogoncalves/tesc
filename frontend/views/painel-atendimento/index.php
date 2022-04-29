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
use kartik\widgets\TimePicker;

use common\models\SolicitacaoTransporte;
use common\models\Escola;
use common\models\Condutor;
use common\models\Aluno;
use common\models\Usuario;
// $this->title = $titulo;

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
$arrayAnos = [];
$this->title = ' Alunos Atendidos - Frete';
$this->params['breadcrumbs'][] = ['label' => 'Painel de atendimento', 'url' => ['index']];
$pdfHeader = [
	'L'    => [
		'content' => '<img src="img/brasao.png" class="img" width="100" />',
	],
	'C'    => [
		'content'     => '<div class="texto kv-align-left"><b>Secretaria de Educação e Cidadania</b><br>Setor de Transporte Escolar<br>Email: transporte.escolar@sjc.gov.br<br>Telefone: 3901-2165</div><br><br><br>',
		'font-size'   => 10,
		'font-style'  => '',
		'font-family' => 'arial',
		'color'       => '#333333',
	],
	'R'    => [
		'content' => 'RIGHT CONTENT (HEAD)',
	],
	'line' => true,
];
$pdfFooter = [
	'L'    => [
		'content'     => '',
		'font-size'   => 10,
		'color'       => '#333333',
		'font-family' => 'arial',
	],
	'C'    => [
		'content' => '',
	],
	'R'    => [
		'content'     => '{PAGENO}',
		'font-size'   => 10,
		'color'       => '#333333',
		'font-family' => 'arial',
	],
	'line' => true,
];
?>

<div class="row">
	<div class="col-md-12"> 
		<div class="box box-solid">
	 
			<div class="box-body">
				<?= Html::beginForm(['painel-atendimento/index'], 'GET', ['id' => 'formFilter']); ?>
				<div class="col-md-12">
					<h4>Filtros</h4>
				</div>
			    <div class="row form-group">
						<div class="col-md-3">
					    <?php
					    	echo Html::label('Tipo de frete', 'tipoFrete');
								echo Select2::widget([
									'name' => 'tipoFrete',
							    'attribute' => 'tipoFrete',
							    'data' => $tiposFrete,
									'value' => $_GET['tipoFrete'] ? $_GET['tipoFrete'] : '',
							    'options' => ['placeholder' => 'Selecione o tipo do frete'],
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
							    'attribute' => 'escola',
							    'data' => $escolas,
									'value' => $_GET['escola'] ? $_GET['escola'] : '',
							    'options' => ['placeholder' => 'Selecione uma escola'],
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
							    'data' => $unidades,
									'value' => $_GET['unidade'] ? $_GET['unidade'] : '',
							    'options' => ['placeholder' => 'Selecione a unidade'],
							    'pluginOptions' => [
							        'allowClear' => true
							    ],
								]);
					    ?>
				    </div>
						<div class="col-md-3">
					    <?php
					    	echo Html::label('Região', 'regiao');
								echo Select2::widget([
									'name' => 'regiao',
							    'attribute' => 'regiao',
									'value' => $_GET['regiao'] ? $_GET['regiao'] : '',
							    'data' => $regioes,
							    'options' => ['placeholder' => 'Selecione uma região'],
							    'pluginOptions' => [
							        'allowClear' => true
							    ],
								]);
					    ?>
				    </div>

						<div class="col-md-2">
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
									'class' => 'form-control',
										'placeholder' => 'Selecione o periodo'
								]
							]);
						?>
					</div>

					<div class="col-md-3">
						<?php
						echo Html::label('Condutor', 'idCondutor');
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
						echo Html::label('Bairro', 'bairro');
						echo Select2::widget([
							'name' => 'bairro',
							'attribute' => 'bairro',
							'data' => $bairros,
								'value' => $_GET['bairro'] ? $_GET['bairro'] : '',
							'options' => ['placeholder' => 'Selecione o bairro'],
							'pluginOptions' => [
								'allowClear' => true
							],
						]);
						?>
					</div>

					<div class="col-md-2">
						<?php
						echo Html::label('Horário de entrada', 'horarioEntrada');
						echo TimePicker::widget([
							'name' => 'horarioEntrada',
							'attribute' => 'horarioEntrada',
							'value' => $_GET['horarioEntrada'] ? $_GET['horarioEntrada'] : '',
							'options' => ['placeholder' => 'Selecione o horário de entrada'],
							'pluginOptions' => [
								'defaultTime' => false,
								'showSeconds' => false,
								'showMeridian' => false,
								'minuteStep' => 1,
								'secondStep' => 5,
							]
						]);
						 
						?>
					</div>
					<div class="col-md-2">
						<?php
						echo Html::label('Horário de saída', 'horarioSaida');
						echo TimePicker::widget([
							'name' => 'horarioSaida',
							'attribute' => 'horarioSaida',
							'value' => $_GET['horarioSaida'] ? $_GET['horarioSaida'] : '',
							'options' => ['placeholder' => 'Selecione o horário de entrada'],
							'pluginOptions' => [
								'defaultTime' => false,
								'showSeconds' => false,
								'showMeridian' => false,
								'minuteStep' => 1,
								'secondStep' => 5,
							]
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
						</div>
			    <div class="row">
				    <div class="col-md-12">
				    	<?= Html::submitButton('Processar', ['class' => 'btn btn-primary pull-right']) ?>
						
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
                    <?= '<span class="label label-primary">Total: '.count($solicitacoesTransporte).'</span>'; ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
	<section class="col-md-12">
		<div class="box box-solid">
		  <div class="box-header with-border">
		    <h4><?= $this->title ?></h4>
		  </div>

		  <div class="box-body">
		    <?php Pjax::begin(); ?>
		      <?= GridView::widget([
				// 'panel' => [
				// 	'heading'=>false,
				// 	'type'=>false,
				// 	'showFooter'=>false
				// ],
				// 'toolbar' =>  [
				// 	'{export}{toggleData}',
				// ],
				'panel' => [
					'heading'=>false,
					'type'=>false,
					'showFooter'=>false
				],
				'toolbar' => \Yii::$app->showEntriesToolbar->create(),
		        'dataProvider' => new ArrayDataProvider([
		          'allModels' => $solicitacoesTransporte,
		          'sort' => [
		              'attributes' => [
		                // 'aluno.nome',
		                // 'idEscola',
		                // 'aluno.RA',
		                // 'aluno.RAdigito',
		              ],
				  ],
				  'pagination' => [
						'pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20,
					],
		        ]),
		        'columns' => [

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
						'value' => 'aluno.nome'
					],

					[
						'attribute' => 'aluno.RA',
						'label' => 'RA',
						'value' => function($model){
							return $model->aluno->RA.'-'.$model->aluno->RAdigito;
						}
					],
					[
						'attribute' => 'aluno.horarioEntrada',
						'label' => 'Horário de entrada',
						'value' => 'aluno.horarioEntrada'
					], 
					[
						'attribute' => 'aluno.horarioSaida',
						'label' => 'Horário de saída',
						'value' => 'aluno.horarioSaida'
					],
					[
						'attribute' => 'ano',
						'label' => 'Ano/Série',
						'value' =>   function($model){
							return $model->aluno->turma ? Aluno::ARRAY_SERIES[$model->aluno->serie].'/'.Aluno::ARRAY_TURMA[$model->aluno->turma] : '-';
						},
					],
                    [
                        'attribute' => 'distanciaEscola',
                        'value' =>  function ($model) {
                            return $model->distanciaEscola . ' KM';
                        },
                    ],
                    [
                        'attribute' => 'barreiraFisica',
                        'value' => function ($model) {
                            return $model->barreiraFisica == 1 ? 'SIM' : 'NÃO';
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
						'attribute' => 'aluno.bairro',
						'value' => 'aluno.bairro',
						'contentOptions' => ['style' => 'min-width:80px;'],
					], 
					[
						'label' => 'Condutor Entrada',
						'value' => function($model){
							return $model->rotaIda ? $model->rotaIda->condutor->nome : '-';
						}
					],
					[
						'label' => 'Telefone Condutor Entrada',
						'value' => function($model){
							return $model->rotaIda ? $model->rotaIda->condutor->celular : '-';
						},
						'visible' => (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
								\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
								\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
					],
					[
						'label' => 'Condutor Saída',
						'value' => function($model){
							return $model->rotaVolta ? $model->rotaVolta->condutor->nome : '-';
						}
					],
					[
						'label' => 'Telefone Condutor Saída',
						'value' => function($model){
							return $model->rotaIda ? $model->rotaIda->condutor->celular : '-';
						},
						'visible' => (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
								\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
								\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
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
					[
						'attribute' => 'status',
						'label' => 'Início do atendimento',
						'value' => function($model){
							$data = $model->atendimento->dataCadastro;
							//return ($model && $data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-';
							
							if($model->id){
								$sql ='select h.dataCadastro from SolicitacaoStatus h where h.idSolicitacaoTransporte = '.$model->id.' and h.`status` = 6 order by id desc limit 1 ';
								$sqlTemIrmao = Yii::$app->getDb()->createCommand($sql)->queryAll();
								if($sqlTemIrmao[0]['dataCadastro']){	
									return ($sqlTemIrmao[0]['dataCadastro'] != '0000-00-00')?Yii::$app->formatter->asDate($sqlTemIrmao[0]['dataCadastro'], 'dd/MM/Y'):'-';
								}else{
									return ($model && $data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-';
								}
							}else{
								return ($model && $data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-';
							}
						}
					],
		        ],
				'exportConfig' => [
					GridView::EXCEL => true,
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
		ordenacoes.push({value: 'CondutorSaida.nome A-Z' , label: 'Condutor Saída - A-Z'});
		ordenacoes.push({value: 'CondutorSaida.nome Z-A' , label: 'Condutor Saída - Z-A'});
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

		// console.warn(value ,value != true)
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
$( document ).ready(function() {
		setTimeout(() => gerarOpcoesOrdenacao(1), 500)
	});
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
		let get = window.location.search;

		get = get.replace('&report=xls', '');
		
		window.open(get+'&report=pdf')
	}

	function gerenciadorXls(){
		let get = window.location.search;

		get = get.replace('&report=pdf', '');
		
		window.open(get+'&report=xls')
	}

	setInterval(() => {
		itens = $("#w11 li").remove();
        item = $("#w11 li")[itens.length-1];
        // if($(item).prop('title') != 'Portable Document Format') {
            $("#w11").append('<li id="meuXls" title="Excel"><a onclick="gerenciadorXls()" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>')
            $("#w11").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')
        // }
	}, 500);
</script>
