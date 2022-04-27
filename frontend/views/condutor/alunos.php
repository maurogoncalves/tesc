<!-- ALUNOS DE CONDUTOR -->
<?php

use common\models\Aluno;
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
use common\models\CondutorRota;
use common\models\HistoricoMovimentacaoRota;
$this->title = $titulo;
$arrayAnos = [];

// if (!$data)
// {
$escolas = [];

foreach($model->escolas as $escola) {
	$escolas[] = $escola->escola;
}


if($model->pendencias){
	$pendencia = 1;
}else{
	$pendencia = 0;
}	
	
?>
<style type="text/css">
	#w1 {
		display: none;
	}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4>
                    <?= '<span class="label label-primary">Total: '.count($data).'</span>'; ?>
                    

					<?php //echo Html::a('Emitir Lista de Alunos Transportados', ['condutor/exportar-alunos-transportados', 'tipo' => 'PDF'], ['class' => 'btn btn-primary pull-right ']); ?>
					<?= Html::a('Emitir Lista de Alunos Transportados', ['painel-atendimento/gerenciar-exportacao', 'idCondutor' => \Yii::$app->User->identity->condutor->id ], ['target' => '_blank', 'class' => 'btn btn-primary pull-right ']); ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
			<div class="box-header with-border">
				<h4>Filtros</h4>
			</div>
			<div class="box-body">
			    <?= Html::beginForm(['condutor/alunos'], 'GET', ['id' => 'formFilter']); ?>
			    <div class="row form-group">
				    <div class="col-md-3">
                    <?php
					    	echo Html::label('Aluno', 'aluno');
					    	echo Html::textInput('aluno', isset($_GET['aluno']) ? $_GET['aluno'] : '',['class'=>'form-control']);
					    ?>
				    </div> 

				    <div class="col-md-3">
					    <?php
					    	echo Html::label('Escola', 'escola');
					    	echo Html::dropdownList('escola', isset($_GET['escola']) ? $_GET['escola'] : '', ArrayHelper::map($escolas, 'id', 'nomeCompleto'),['id' => 'escola', 'prompt' => 'Selecione', 'class' => 'form-control']);
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
			<span id="w66"></span>
			<div class="box-body">
	           <?= GridView::widget([
					  'panel' => [
                        'heading'=>false,
                        'type'=>false,
                        'showFooter'=>false
                    ],
                     'summary' => "Exibindo <b>{begin}</b>-<b>{end}</b> de <b>{totalCount}</b> itens.",

                    'toolbar' => \Yii::$app->showEntriesToolbar->create(),
	                'dataProvider' => new ArrayDataProvider([
					    'allModels' => $data,
					    'sort' => [
					        'attributes' => ['nome','escola.nome'],
					    ],
					  'pagination' => [
						'pageSize' =>isset($_GET['pageSize']) ? $_GET['pageSize'] : 20,
					],
					]),

	                // 'filterModel' => $searchModel,
	                // 'showPageSummary' => true,
					
	                'columns' => [
						[
							'header' => 'Ciente',
							'class' => 'kartik\grid\CheckboxColumn',
							'headerOptions' => ['class' => 'kartik-sheet-style'],
							'checkboxOptions' =>
								function($model) {
									return ['value' => $model['id'], 'class' => 'checkbox-ciente', 'id' => 'checkbox' ,'checked' => $model['naoEstaUtilizando']];
								},
							'hAlign'=>'center',
							'vAlign'=>'middle',
							'hiddenFromExport'=>true,
							'mergeHeader'=>true,

						],
						[
							'header' => 'Alunos que não estão usando o benefício',
							'class' => 'kartik\grid\CheckboxColumn',
							'headerOptions' => ['class' => 'kartik-sheet-style'],
							'checkboxOptions' =>
								function($model) {
									return ['value' => $model['id'], 'class' => 'checkbox-row', 'id' => 'checkbox' ,'checked' => $model['naoEstaUtilizando']];
								},
							'hAlign'=>'center',
							'vAlign'=>'middle',
							'hiddenFromExport'=>true,
							'mergeHeader'=>true,

						],
	                // ['class' => 'yii\grid\SerialColumn'],
                    // 'id',
					[
	                    'attribute' => 'data',
	                    'label' => 'Data Início Benefício',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
	                    'value' => function($model){
	                        return $model['dataSol'];
	                    }
					],
					[
	                    'attribute' => 'idEscola',
	                    'label' => 'Escola',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
	                    'value' => function($model){
	                        return $model['escola'];
	                    }
					],
					 [
	                    'attribute' => 'nome',
	                    'label' => 'Nome',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
	                    'value' => function($model){
	                        return $model['nome'];
	                    }
					],
	                [
	                    'attribute' => 'RA',
	                    'label' => 'RA',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
	                    'value' => function($model){
	                        return $model['RA'].' '.$model['RAdigito'];
	                    }
					],
					[
						'attribute' => 'serie',
						'label' => 'Ano/Série/Turma',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' =>   function($model){
                            if(isset($model['serie']) && isset($model['turma'])) {
                            return  Aluno::ARRAY_SERIES[$model['serie']].'/'.Aluno::ARRAY_TURMA[$model['turma']];
                            }
						},
						'filter' => Aluno::ARRAY_SERIES
					],
					[
						'attribute' => 'turno',
						'label' => 'Turno',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' =>   function($model){
                            if(isset($model['turno'])) {
                            return  Aluno::ARRAY_TURNO[$model['turno']];
                            }
						},
						'filter' => Aluno::ARRAY_TURNO
					],
					[
						'attribute' => 'horarioEntrada',
						'label' => 'Horário de Entrada',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' => function($data) use ($model) {
							// if ($data->solicitacaoAtiva)
								// if ($data->solicitacaoAtiva->rotaIda)
									// if ($data->solicitacaoAtiva->rotaIda->idCondutor == $model->id)
										// return $data->horarioEntrada;
									
							// return '-';
							return $data['horarioEntrada'];
						}
					],
					[
						'attribute' => 'horarioSaida',
						'label' => 'Horário de Saída',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' => function($data) use ($model){
							// if ($data->solicitacaoAtiva)
								// if ($data->solicitacaoAtiva->rotaVolta)
									// if ($data->solicitacaoAtiva->rotaVolta->idCondutor == $model->id)
										// return $data->horarioSaida;
								
							// return '-';
							return $data['horarioSaida'];
						}
					],
					 [
						'attribute' => 'nomeMae',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'label' => 'Nome da mãe'
					 ],
					 [
						 'attribute' => 'nomePai',
						 'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						 'label' => 'Nome do pai'
					 ],
					 [
						'attribute' => 'endereco',
						'label' => 'Endereço',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' => function($model) {
							return $model['endereco'].' '.$model['numeroResidencia'];
						}
					],
					[
						'attribute' => 'bairro',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'label' => 'Bairro'
					],

					[
						'attribute' => 'telefoneResidencial',
						'label' => 'Telefones',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' => function($model) {
							$tel='';
							if($model['telefoneCelular'] && strlen($model['telefoneCelular']) > 5)
								 $tel .= $model['telefoneCelular'].'/';
							if($model['telefoneCelular2'] && strlen($model['telefoneCelular2']) > 5)
								 $tel .= $model['telefoneCelular2'].'/';
							if($model->telefoneResidencial && strlen($model['telefoneResidencial']) > 5)
								 $tel .= $model['telefoneResidencial'].'/';
							if($model['telefoneResidencial2'] && strlen($model['telefoneResidencial2']) > 5)
								 $tel .= $model['telefoneResidencial2'].'/';

							// return '-';
							return substr($tel, 0, -1);
						}
					],

					[
						'attribute' => 'telefoneResidencial',
						'label' => 'Início',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' => function($model) {
							//return $model->entradaRota();
							return $model['dataSol'];
						}
					],


					[
						'attribute' => 'telefoneResidencial',
						'label' => 'Viagem Ida',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' => function($model) {
							return $model['idRotaIda'];
							// $condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
							// foreach($model->meusPontos as $alunoPonto) {
								// if($alunoPonto->ponto->condutorRota->sentido == CondutorRota::SENTIDO_IDA)
										// return $alunoPonto->ponto->condutorRota->viagem;
							// }
						}
					],

										[
						'attribute' => 'telefoneResidencial',
						'label' => 'Viagem Volta',
						'contentOptions'=>function($model, $key, $index, $column) { 
							if($model['alunoNovo'] == '1' and $model['cienteCondutor'] == '0')
								return ['style' => 'color:red'];  
							else
								return ['style' => ''];  
						 },	 
						'value' => function($model) {
							return $model['idRotaVolta'];
							// // $condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
							// foreach($model->meusPontos as $alunoPonto) {
								// if($alunoPonto->ponto->condutorRota->sentido == CondutorRota::SENTIDO_VOLTA)
										// return $alunoPonto->ponto->condutorRota->viagem;
							// }
						}
					],
					
				 
                     [
                        'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna                
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{roterizar}', 
                        'buttons' => [
                        'roterizar' => function ($url, $model) {
                            //return  Html::a('<i class="fa fa-street-view" aria-hidden="true"></i>', Url::to(['condutor-rota/roterizar', 'idCondutorRota' => $model->alunoPontoRota->ponto->idCondutorRota]), ['data-pjax' => 0,'target' => '_blank', 'title' => Yii::t('app', 'Rota'),
							return  Html::a('<i class="fa fa-street-view" aria-hidden="true"></i>', Url::to(['condutor-rota/roterizar', 'idCondutorRota' => $model['idRotaVolta']]), ['data-pjax' => 0,'target' => '_blank', 'title' => Yii::t('app', 'Rota'),
                                ]);
                        },
                        ]
                    ]

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
	            ]); ?>
	        </div>
		</div>
	</section>
</div>

<?php //} ?>

<script type="text/javascript">

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
	async function processarJustificativa(idAluno,marcado=null, justificativa='') {
		return await $.post( "index.php?r=condutor/beneficio-aluno", { idAluno: idAluno, justificativa: justificativa, marcado: marcado })
	
	}
	
	$('.checkbox-ciente').change(async function() {
		var idAluno = $(this).val();
	
		if($(this).is(":checked")) {
			
			  Swal.fire({
						title: '😊 Atenção usuário(a)!',
						text: 'Está ciente que precisa entrar em contato com o responsável do estudante?',
						icon: 'warning',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Sim, estou',
					}).then((result) => {
						$.ajax({	
							type: 'POST',
							url: 'index.php?r=condutor/salvar',
								data:{
								  idCondutor: '<?php echo(\Yii::$app->User->identity->condutor->id)?>',
								  tipo: '2',
								  aluno: idAluno
								},
							}).done(function(data) {
								window.location.href = 'index.php?r=condutor/alunos/';
							
						});
					});	
					
		
		} 
		
	});	
	
	$('.checkbox-row').change(async function() {
		// returnStatus = true;

		var idAluno = $(this).val();
		if(!$(this).is(":checked")) {
			let x  = null; 
			x = await processarJustificativa(idAluno,null)
			return null;
		}
        if($(this).is(":checked")) {
			Swal.fire({
				title: 'Digite a justificativa',
				input: 'text',
				inputAttributes: {
					autocapitalize: 'off'
				},
				showCancelButton: true,
				confirmButtonText: 'Salvar',
				showLoaderOnConfirm: true,
				preConfirm: async(justificativa) => {
					let x  = null; 
					x = await processarJustificativa(idAluno,1, justificativa)
					if(x.status == true) {
					$(this).prop('checked', true);

						return Swal.fire(
							'Sucesso',
							'Obrigado por nos informar',
							'success'
							)
					} else {
						$(this).prop('checked', false);
						$(this).closest('tr').removeClass('danger')

					}
				},
				allowOutsideClick: () => !Swal.isLoading()
				})
				.then((result) => {
				if (result.dismiss === Swal.DismissReason.cancel) {
					$(this).prop('checked', false);
					$(this).closest('tr').removeClass('danger')
				

				}
				})
				
            // var returnVal = confirm("Tem certeza?");
            // $(this).attr("checked", returnVal);
		} 


        // $(thi).val($(this).is(':checked'));        
    });
</script>


<script> 
$(document).ready(function() {
	
setTimeout(() => $("#w2").html($("#w66").html()), 200)

   
	
	if('<?php echo $alunoNovo?>' == 1){
			let TextoNovosAlunos = '<br> <p align="left"> <b>NOVO(S) ALUNO(S) INSERIDO(S) EM SUA LISTA</b> <br><br> ';
			TextoNovosAlunos =  TextoNovosAlunos +  ' Por favor, entrar em contato com o responsável do estudante. </p>';

			  Swal.fire({
				title: 'Atenção condutor(a)!',
				html: TextoNovosAlunos,
				icon: 'warning',
				showCancelButton: false,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Ok, vou verificar',
			}).then((result) => {
				 let Texto = '<p align="left" > <b>Condutor :</b> <br><br> ';
				 Texto =  Texto +  ' Regularize as pendência(s) para não incorrer nas penalidades previstas no contrato de prestação de serviço <br>  e legislação pertinente. <br><br>';
				 Texto =  Texto +  ' Verifique a pendência no seu perfil. </p>';
				 console.log('oi');
				 if('<?php echo $pendencia?>' == 1){
					  Swal.fire({
						title: 'Atenção condutor(a)!',
						html: Texto,
						icon: 'warning',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ok, vou verificar',
					}).then((result) => {
						$.ajax({	
							type: 'POST',
							url: 'index.php?r=condutor/salvar',
								data:{
								  idCondutor: '<?php echo(\Yii::$app->User->identity->condutor->id)?>',
								  tipo: '1',
								  aluno: '0'
								},
							}).done(function(data) {
							
						});
					});	
				}
			});
		}else{
			 if('<?php echo $pendencia?>' == 1){
					 let Texto = '<p align="left" > <b>Condutor :</b> <br><br> ';
						Texto =  Texto +  ' Regularize as pendência(s) para não incorrer nas penalidades previstas no contrato de prestação de serviço <br>  e legislação pertinente. <br><br>';
						Texto =  Texto +  ' Verifique a pendência no seu perfil. </p>';
					  Swal.fire({
						title: '😊 Atenção usuário(a)!',
						html: Texto,
						icon: 'warning',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ok, vou verificar',
					}).then((result) => {
						console.log('OK VOU VERIFICAR')
					});	
			}
		}
	
	
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
        // let keys = $('#w0').yiiGridView('getSelectedRows');
        let checkboxes = 0;       
        
        
        let x='todos os registros de alunos?';
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
              window.open('index.php?r=condutor/exportar&selecionados='+checkboxes+'&tipo='+tipo)
            }
          })


    }

		if('<?php echo $alunoNovo?>' == 1){
			let TextoNovosAlunos = '<br> <p align="left"> <b>NOVO(S) ALUNO(S) INSERIDO(S) EM SUA LISTA</b> <br><br> ';
			TextoNovosAlunos =  TextoNovosAlunos +  ' Por favor, entrar em contato com o responsável do estudante. </p>';

			  Swal.fire({
				title: 'Atenção condutor(a)!',
				html: TextoNovosAlunos,
				icon: 'warning',
				showCancelButton: false,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Ok, vou verificar',
			}).then((result) => {
				 let Texto = '<p align="left" > <b>Condutor :</b> <br><br> ';
				 Texto =  Texto +  ' Regularize as pendência(s) para não incorrer nas penalidades previstas no contrato de prestação de serviço <br>  e legislação pertinente. <br><br>';
				 Texto =  Texto +  ' Verifique a pendência no seu perfil. </p>';
				 if('<?php echo $pendencia?>' == 1){
					  Swal.fire({
						title: 'Atenção condutor(a)!',
						html: Texto,
						icon: 'warning',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ok, vou verificar',
					}).then((result) => {
						$.ajax({	
							type: 'POST',
							url: 'index.php?r=condutor/salvar',
								data:{
								  idCondutor: '<?php echo(\Yii::$app->User->identity->condutor->id)?>',
								  tipo: '1',
								  aluno: '0'
								},
							}).done(function(data) {
							
						});
					});	
				}
			});
		}else{
			 if('<?php echo $pendencia?>' == 1){
					 let Texto = '<p align="left" > <b>Condutor :</b> <br><br> ';
						Texto =  Texto +  ' Regularize as pendência(s) para não incorrer nas penalidades previstas no contrato de prestação de serviço <br>  e legislação pertinente. <br><br>';
						Texto =  Texto +  ' Verifique a pendência no seu perfil. </p>';
					  Swal.fire({
						title: '😊 Atenção usuário(a)!',
						html: Texto,
						icon: 'warning',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ok, vou verificar',
					}).then((result) => {
						console.log('OK VOU VERIFICAR')
					});	
				}
		}
	
	
 
	
   
</script>