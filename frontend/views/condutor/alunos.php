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
							'header' => 'Alunos que não estão usando o benefício',
							'class' => 'kartik\grid\CheckboxColumn',
							'headerOptions' => ['class' => 'kartik-sheet-style'],
							'checkboxOptions' =>
								function($model) {
									return ['value' => $model->id, 'class' => 'checkbox-row', 'id' => 'checkbox' ,'checked' => $model->naoEstaUtilizando];
								},
							'hAlign'=>'center',
							'vAlign'=>'middle',
							'hiddenFromExport'=>true,
							'mergeHeader'=>true,

						],
	                // ['class' => 'yii\grid\SerialColumn'],
                    // 'id',
                    [
	                	'attribute' => 'idEscola',
                        'value' => 'escola.nome',
						'label' => 'Escola',
	                ],
	                'nome',
	                 // 'dataNascimento',
	                [
	                    'attribute' => 'RA',
	                    'label' => 'RA',
	                    'value' => function($model){
	                        return $model->RA.' '.$model->RAdigito;
	                    }
					],
					[
						'attribute' => 'serie',
						'label' => 'Ano/Série/Turma',
						'value' =>   function($model){
                            if(isset($model->serie) && isset($model->turma)) {
                            return  Aluno::ARRAY_SERIES[$model->serie].'/'.Aluno::ARRAY_TURMA[$model->turma];
                            }
						},
						'filter' => Aluno::ARRAY_SERIES
					],
					[
						'attribute' => 'turno',
						'label' => 'Turno',
						'value' =>   function($model){
                            if(isset($model->turno)) {
                            return  Aluno::ARRAY_TURNO[$model->turno];
                            }
						},
						'filter' => Aluno::ARRAY_TURNO
					],
					[
						'attribute' => 'horarioEntrada',
						'label' => 'Horário de Entrada',
						'contentOptions' => ['style' => 'min-width:80px;'],
						'value' => function($data) use ($model) {
							if ($data->solicitacaoAtiva)
								if ($data->solicitacaoAtiva->rotaIda)
									if ($data->solicitacaoAtiva->rotaIda->idCondutor == $model->id)
										return $data->horarioEntrada;
									
							return '-';
						}
					],
					[
						'attribute' => 'horarioSaida',
						'label' => 'Horário de Saída',
						'contentOptions' => ['style' => 'min-width:80px;'],
						'value' => function($data) use ($model){
							if ($data->solicitacaoAtiva)
								if ($data->solicitacaoAtiva->rotaVolta)
									if ($data->solicitacaoAtiva->rotaVolta->idCondutor == $model->id)
										return $data->horarioSaida;
								
							return '-';
						}
					],
					 [
						'attribute' => 'nomeMae',
						'label' => 'Nome da mãe'
					 ],
					 [
						 'attribute' => 'nomePai',
						 'label' => 'Nome do pai'
					 ],
					 [
						'attribute' => 'endereco',
						'label' => 'Endereço',
						'value' => function($model) {
							return $model->enderecoCompleto();
						}
					],
					[
						'attribute' => 'bairro',
						'label' => 'Bairro'
					],

					[
						'attribute' => 'telefoneResidencial',
						'label' => 'Telefones',
						'contentOptions' => ['style' => 'min-width:80px;'],
						'value' => function($model) {
							$tel='';
							if($model->telefoneCelular && strlen($model->telefoneCelular) > 5)
								 $tel .= $model->telefoneCelular.'/';
							if($model->telefoneCelular2 && strlen($model->telefoneCelular2) > 5)
								 $tel .= $model->telefoneCelular2.'/';
							if($model->telefoneResidencial && strlen($model->telefoneResidencial) > 5)
								 $tel .= $model->telefoneResidencial.'/';
							if($model->telefoneResidencial2 && strlen($model->telefoneResidencial2) > 5)
								 $tel .= $model->telefoneResidencial2.'/';

							// return '-';
							return substr($tel, 0, -1);
						}
					],

					[
						'attribute' => 'telefoneResidencial',
						'label' => 'Início',
						'contentOptions' => ['style' => 'min-width:80px;'],
						'value' => function($model) {
							return $model->entradaRota();
						}
					],


					[
						'attribute' => 'telefoneResidencial',
						'label' => 'Viagem Ida',
						'contentOptions' => ['style' => 'min-width:80px;'],
						'value' => function($model) {
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
						'contentOptions' => ['style' => 'min-width:80px;'],
						'value' => function($model) {
							// $condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
							foreach($model->meusPontos as $alunoPonto) {
								if($alunoPonto->ponto->condutorRota->sentido == CondutorRota::SENTIDO_VOLTA)
										return $alunoPonto->ponto->condutorRota->viagem;
							}
						}
					],
					
				 
                     [
                        'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna                
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{roterizar}', 
                        'buttons' => [
                        'roterizar' => function ($url, $model) {
                            return  Html::a('<i class="fa fa-street-view" aria-hidden="true"></i>', Url::to(['condutor-rota/roterizar', 'idCondutorRota' => $model->alunoPontoRota->ponto->idCondutorRota]), ['data-pjax' => 0,'target' => '_blank', 'title' => Yii::t('app', 'Rota'),
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
	async function processarJustificativa(idAluno,marcado=null, justificativa='') {
		return await $.post( "index.php?r=condutor/beneficio-aluno", { idAluno: idAluno, justificativa: justificativa, marcado: marcado })
	
	}
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
</script>