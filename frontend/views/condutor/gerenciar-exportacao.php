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
use  yii\web\Session;
use kartik\export\ExportMenu;
	$get = $_GET;
	$this->title = $titulo;
	$arrayAnos = [];
	$this->title = ' Alunos por Condutor';
	$this->params['breadcrumbs'][] = ['label' => 'Painel de atendimento', 'url' => ['index']];
	?>
        <style>
        #meuPdf {
            cursor:pointer;
        }
        li {
        	  list-style-type: none; 
        }
        </style>
	<div class="row">

		<section class="col-md-12" style="display:none;"> 
			<div class="box box-solid">
			<div class="box-body">
				<?php Pjax::begin();
				
				$toolbar = [];
				$toolbar[]= Html::a('<i class="glyphicon glyphicon-export"></i>', ['#'], ['class' => 'btn btn-default','onclick'=>"gerenciarExportacao(event)"]);
				$toolbar[]= Html::a('<i class="glyphicon glyphicon-export"></i>', ['#'], ['class' => 'btn btn-default','onclick'=>"gerenciarExportacaoAtivo(event)"]);
   
				$toolbar[]='{toggleData}';
				?>
				<?= $x = GridView::widget([
					
					'panel' => [
						'heading'=>false,
						'type'=>false,
						'showFooter'=>false
					],
					'toolbar' =>  $toolbar,
					'dataProvider' => new ArrayDataProvider([
					
					'allModels' => $dados,
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
						'pageSize' => 10000,
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
									return $model->solicitacaoAtiva->rotaIda->condutor->telefone;
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
	<div class="row">
		<div class="col-md-12">
		<h4>Selecione o formato do arquivo para exportação</h4>

	
    <?php 
// echo "Confirma a exportação de ".count($dados)." registros?";
echo '<ul>' . ExportMenu::widget([
    'exportConfig' => [
        ExportMenu::FORMAT_PDF => false,
		ExportMenu::FORMAT_EXCEL => false,
		
    ],
    'dataProvider' => new ArrayDataProvider([
					
        'allModels' => $dados,
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
            'pageSize' => 10000,
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
                    return $model->solicitacaoAtiva->rotaIda->condutor->telefone;
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
	'showConfirmAlert' => false,
    'asDropdown' => false
]) . '</ul>';
?>
	</div>
	</div>
	<script type="text/javascript">
			var idCondutor = '<?= isset($get['idCondutor']) ?  $get['idCondutor'] : '' ?>';
	var escola = '<?= isset($get['escola']) ?  $get['escola'] : '' ?>';
	var pageSize = '<?= isset($get['pageSize']) ?  $get['pageSize'] : '' ?>';
	var sort = '<?= isset($get['sort']) ?  $get['sort'] : '' ?>';

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

		let get = '';
			if(idCondutor) {
				get += '&idCondutor='+idCondutor;
			}
			if(escola) {
				get += '&escola='+escola;
			}

		window.open('index.php?r=painel-atendimento/gerenciar-exportacao'+mountGet()+'&selecionados='+checkboxes)


	}


	// console.log(indexes);
	
	// setInterval(() => {
	// 	var keys = $('#w2').yiiGridView('getSelectedRows');
	// 	console.log(keys);
	// }, 500)
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
            window.location.href = 'index.php?r=painel-atendimento/report'+get
		}
		
		setInterval(() => {
	// itens = $(".p-0 ul");
			itens = $("ul");
        let lis = $(itens).find('li')
     
		// item = $("li")[itens.length-1];
            // console.log(itens);
        // console.log(itens[itens.length-1]);
            if( !$("#meuPdf").html()) {
            $(lis[3]).append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')
            }
		
		}, 500);



	</script>



