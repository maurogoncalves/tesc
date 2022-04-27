<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use common\models\Condutor;
use common\models\Veiculo;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $searchModel common\models\CondutorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Controle financeiro';
$this->params['breadcrumbs'][] = $this->title;


			
?>
<style>

</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-body">
                <?= Html::beginForm(['condutor/controle-financeiro'], 'GET', ['id' => 'formFilter']); ?>
                <div class="row form-group">
                    <div class="col-md-4">
                    <?php
                        echo Html::label('Ano', 'ano');
                        echo Select2::widget([
                            'name' => 'ano',
                            'attribute' => 'ano',
                            'data' => Yii::$app->params['arrayAnos'],
                            'value' => $_GET['ano'] ? $_GET['ano'] : '',
                            'options' => ['placeholder' => 'Selecione o ano'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                        echo Html::label('Mês', 'mes');
                        echo Select2::widget([
                            'name' => 'mes',
                            'attribute' => 'mes',
                            'data' => Yii::$app->params['arrayMeses'],
                            'value' => $_GET['mes'] ? $_GET['mes'] : '',
                            'options' => ['placeholder' => 'Selecione o mês'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?= Html::submitButton('Confirmar', ['class' => 'btn btn-primary pull-right']) ?>
                </div>
            </div>

            <?php echo Html::endForm(); ?>
        </div>

        <div class="row">



    <div class="col-md-12">
		<div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
					<div class="col-md-3"> <b>Mês/Ano : <?php echo$mes.'/'.$ano?></b></div>
					<div class="col-md-3" style='color:#000'> <b>Marque os dias (disponíveis) em branco para bloquear </b></div>
					<div class="col-md-3" style='color:#000'> <b>Marque os dias (bloqueados) em cinza para liberar </b></div>
					<div class="col-md-3" style='color:#000'> <b>Clique no botão Salvar </b></div>
					
                </div>
            </div>
        </div>
		<form method='post' id='formData' action='index.php?r=condutor%2Fgravar-supervisor'> 
		<input type='hidden'  name="_csrf-frontend" value='mH7B72U_mm1c2Knx_Hz17QQ2JgLbIOZHUDOnicXxlkTfPaO_PEz3Dwnt55W9E4GGfXd8MJdw0XEpdpHiscjOHA=='>
		<input type='hidden'  name="mes" id="mes" value='<?php echo$mes?>'>
		<input type='hidden'  name="ano" id="ano" value='<?php echo$ano?>'>
        <div class="box box-solid">
            <div class="box-body">
               <table id="" class="display" style="width:100%">
							<thead>
								<tr>
									<th style="text-align:center;color:#3c8dbc">Dom</th>
									<th style="text-align:center;color:#3c8dbc">Seg</th>
									<th style="text-align:center;color:#3c8dbc">Ter</th>
									<th style="text-align:center;color:#3c8dbc">Qua</th>
									<th style="text-align:center;color:#3c8dbc">Qui</th>
									<th style="text-align:center;color:#3c8dbc">Sex</th>
									<th style="text-align:center;color:#3c8dbc">Sab</th>
								</tr>
							</thead>
							<tbody>
								<tr><td colspan=7 style='border-top: 1px solid white;  border-color:#dedede;' ><br><br></td></tr>				
								<tr>	
								 <?php
									echo ($primeiraSemana);							
								?>																	
								</tr>	
								<tr><td colspan=7 style='border-top: 1px solid white;  border-color:#dedede;' ><BR><BR></td></tr>
								<tr>									
									<?php 
										echo ($segundaSemana);											
									?>					
								</tr>

								<tr><td colspan=7 style='border-top: 1px solid white;  border-color:#dedede;' ><BR><BR></td></tr>
								<tr>									
									<?php 
										echo ($terceiraSemana);																					
									?>					
								</tr>
								
								<tr><td colspan=7 style='border-top: 1px solid white;  border-color:#dedede;' ><BR><BR></td></tr>
								<tr>
									<?php 
										echo ($quartaSemana);																															
									?>					
								</tr>								
								<tr><td colspan=7 style='border-top: 1px solid white;  border-color:#dedede;' ><BR><BR></td></tr>
								<tr>								
									<?php 
										echo ($quintaSemana);																																								
									?>																
								</tr>				
									<tr><td colspan=7 style='border-top: 1px solid white;  border-color:#dedede;' ><BR><BR></td></tr>							
									<tr>								
									<?php 
										echo ($sextaSemana);											
									?>															
								</tr>
								<tr><td colspan=7 style='border-top: 1px solid white;  border-color:#dedede;' ><BR><BR></td></tr>
								</tbody>					
								</table>
				<input type="submit" class="btn btn-success pull-right" name="Salvar" value="Salvar"> 				
            </div>
			
        </div>
		

		</form>
    </div>
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="box box-solid">
					<div class="box-header with-border" style='color:#000;font-size:12px!important;font-weight:bold;text-align:left'>
						<div  class="col-md-12" style='color:#000;font-size:12px!important;font-weight:bold' > Ocorrências </div>
						<div id='ocorrencias' class="col-md-12" style='color:#000;font-size:12px!important;font-weight:bold' > </div>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>

        <?php

        if (Yii::$app->request->get('ano') && Yii::$app->request->get('mes'))
        {
            echo  Html::beginForm(['condutor/save-historico-financeiro'], 'POST', ['id' => 'formData']);
        ?>
            <input type="hidden" name="ano" value="<?= Yii::$app->request->get('ano') ?>" />
            <input type="hidden" name="mes" value="<?= Yii::$app->request->get('mes') ?>" />
            <div class="box-header with-border">
                <h4><?= '<span class="label label-primary">Total: '.$dataProvider->getTotalCount().'</span>'; ?></h4>
            </div>

            <div class="box-body"> 
                <?php Pjax::begin(); ?>  
                <?= GridView::widget([
                    // 'grid-historico' => 'grid-historico',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'containerOptions' => ['class' => 'table-responsive'],
                    'tableOptions' => ['class' => 'table table-bordered'],
                    'pjax' => false,
                    'striped' => false,
                    'emptyText' => 'Nenhum registro encontrado',
                    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                    'filterRowOptions' => ['class' => 'fixed-first'],
                    'panel' => [
                        'heading'=>false,
                        'type'=>false,
                        'showFooter'=>false
                    ],
                    'summary' => "Exibindo <b>{begin}</b>-<b>{end}</b> de <b>{totalCount}</b> itens.",
                    'toolbar' => \Yii::$app->showEntriesToolbar->create(),
                    'exportConfig' => [
                        GridView::EXCEL => true
                    ],
                    'columns' => [
						[
                            'attribute' => 'export',
                            'label' => '',
                            'format' => 'raw',
                            'contentOptions' => ['style' => 'min-width:50px; text-align:center; white-space: normal;'],
                            'value' => function ($model) {
                                return Html::button('<i id='.$model->condutor->id.' class="glyphicon glyphicon-file export"></i>', ['value' => '', 'title' => 'Exportar PDF', 'class' => 'btn btn-clear bth-xs export', 'id' => $model->id]);
                            }
                        ],
						  [
                            'attribute' => 'acao',
                            'label' => '',
                            'format' => 'raw',
                            'contentOptions' => ['style' => 'min-width:100px; white-space: normal; display:inline-block;'],
                            'value' => function ($model) {
                                return  Html::button('<i class="glyphicon glyphicon-search"></i>', ['value' => Url::to(['condutor/view', 'id' => $model->id, 'ajax' => true]), 'title' => 'Detalhes do condutor', 'class' => 'showModalButton btn btn-primary', 'aria-label' => 'Detalhes']);
                            }
                        ],
                        // [
                            // 'attribute' => 'duplicar',
                            // 'label' => '',
                            // 'format' => 'raw',
                            // 'contentOptions' => ['style' => 'min-width:50px; text-align:center; white-space: normal; display:inline-block;'],
                            // 'value' => function ($model) {
                                // return Html::button('<i class="glyphicon glyphicon-plus"></i>', ['value' => '', 'title' => 'Solicitação', 'class' => 'btn btn-clear bth-xs cloneBtn', 'id' => $model->id]);
                            // }
                        // ],
                        [
                            'attribute' => 'nome',
                            'contentOptions' => ['style' => 'min-width:350px;'],
                            'headerOptions' => ['style' => 'min-width:350px;'],
                            'value' => function ($model) {
								
								$ano = Yii::$app->request->get('ano');
								$mes = Yii::$app->request->get('mes');

								$sqlNomeEmpresa ='select c.nomeEmpresa  from ControleFinanceiro c  where c.idCondutor = '.$model->id.' and mes = '.$mes.' and ano = '.$ano ;
								$dadosNomeEmpresa = Yii::$app->getDb()->createCommand($sqlNomeEmpresa)->queryAll();
											
								if($dadosNomeEmpresa[0]['nomeEmpresa']){
									return $dadosNomeEmpresa[0]['nomeEmpresa'];
								}else{
									return $model->condutor->nome;
								}
                                
                            }
                        ],
                        [ 
                            'label' => 'Alvará',
                            'attribute' => 'alvara',
                            'contentOptions' => array('style' => 'min-width:150px;'),
                            'headerOptions' => array('style' => 'min-width:150px;'),
                            'value' => function ($model) {
								
								$ano = Yii::$app->request->get('ano');
								$mes = Yii::$app->request->get('mes');

								$sqlAlvara ='select c.alvara  from ControleFinanceiro c  where c.idCondutor = '.$model->id.' and mes = '.$mes.' and ano = '.$ano ;
								$dadosAlvara = Yii::$app->getDb()->createCommand($sqlAlvara)->queryAll();
											
								if($dadosAlvara[0]['alvara']){
									return $dadosAlvara[0]['alvara'];
								}else{
									return $model->condutor->alvara;
								}
								
                            }
                        ],
                        [
                            'attribute' => 'diasTrabalhados',
                            'header' => 'Dias <br>Trabalhados <br> (1)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[diasTrabalhados]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['diasTrabalhados'].'">',
                            'contentOptions' => ['style' => 'min-width:100px;'],
                            'headerOptions' => ['style' => 'min-width:100px;text-align:center;'],
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control diasTrabalhados" name="diasTrabalhados[]" id="diasTrabalhados_'.$model->id.'" value="'.$historico[$model->id]['diasTrabalhados'].'" />';
                                return $campo;
                            }
                        ],
						[
                            'attribute' => 'valorViagemKm1',
                            'header' => 'Kms rodado / dia <br> Nº viagens / dia <br> (1)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm1]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['valorViagemKm1'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm1" name="viagemKm1[]" id="viagemKm1_'.$model->id.'" value="'.$historico[$model->id]['valorViagemKm1'].'" />';
                                return $campo;
                            }
                        ],
						[
                            'attribute' => 'viagemKm1',
                            'header' => 'Viagem/Km<br>(1)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm1]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['viagemKm1'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm1" name="viagemKm1[]" id="viagemKm1_'.$model->id.'" value="'.$historico[$model->id]['viagemKm1'].'" />';
                                return $campo;
                            }
                        ],
						[
                            'attribute' => 'viagemKm1',
                            'header' => 'Total Dias Trab. <br>(1)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm1]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['valorDiasUteis'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm1" name="viagemKm1[]" id="viagemKm1_'.$model->id.'" value="'.$historico[$model->id]['valorDiasUteis'].'" />';
                                return $campo;
                            }
                        ],
						
						
                        [
                            'attribute' => 'sabadoLetivo',
                            'header' => 'Sábados <br> Trabalhados <br> (2)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[sabadoLetivo]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['sabadoLetivo'].'">',
                            'contentOptions' => ['style' => 'min-width:100px;'],
                            'headerOptions' => ['style' => 'min-width:100px;text-align:center;'],
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control sabadoLetivo" name="sabadoLetivo[]" id="sabadoLetivo_'.$model->id.'" value="'.$historico[$model->id]['sabadoLetivo'].'" />';
                                return $campo;
                            }
                        ],
                       
					   [
                            'attribute' => 'valorViagemKm1',
                            'header' => 'Kms rodado / dia <br> Nº viagens / dia <br> (2)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm1]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['valorViagemKm2'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm1" name="viagemKm1[]" id="viagemKm1_'.$model->id.'" value="'.$historico[$model->id]['valorViagemKm2'].'" />';
                                return $campo;
                            }
                        ],
						
                        [
                            'attribute' => 'viagemKm2',
                            'header' => 'Viagem/Km<br>(2)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm2]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['viagemKm2'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm2" name="viagemKm2[]" id="viagemKm2_'.$model->id.'" value="'.$historico[$model->id]['viagemKm2'].'" />';
                                return $campo;
                            }
                        ],
						[
                            'attribute' => 'viagemKm1',
                            'header' => 'Total Sábados <br>(2)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm1]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['valorSabado'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm1" name="viagemKm1[]" id="viagemKm1_'.$model->id.'" value="'.$historico[$model->id]['valorSabado'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'valorNota',
                            'header' => 'Valor Total <br>Nota',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[valorNota]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['valorNota'].'">',
                            'contentOptions' => array('style' => 'min-width:200px;text-align:center;'),
                            'headerOptions' => array('style' => 'min-width:200px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input size="50" class="form-control valorNota" name="valorNota[]" id="valorNota_'.$model->id.'" readonly value="'.sprintf('%0.2f', $historico[$model->id]['valorNota']).'" />';
                                return $campo;
                            }
                        ],
                       
                      
                    ]
                ]); ?>
                <?php Pjax::end(); ?> 
            </div>

            <div class="box-footer">
                <!--  <?= Html::submitButton('Salvar Dados', ['class' => 'btn btn-success pull-right']) ?>  -->
                <!-- <button class="btn btn-success pull-right" id="saveBtn">Salvar</button> -->
            </div>
        <?php 
            echo Html::endForm();
        }
        else { ?>
        <div class="box-body">
            <div class="callout callout-warning">
                <h4>Atenção!</h4>
                <p>Selecione um ano e mês para prosseguir.</p>
            </div>
        </div>
        <?php } ?>
    </div>
	

</div>

<script type="text/javascript">

function buscar(){
	let mes = $("#mes").val();
	let ano = $("#ano").val();
	$.ajax({	
		type: 'POST',
		url: 'index.php?r=condutor/buscar-ocorrencia',
		data:{
		  buscar: '1',
		  mes : mes,
		  ano : ano,
		},
		}).done(function(data) {
			if(data){			
				$("#ocorrencias").html(data);
			}			
		});
};

$(document).ready(function() {
	buscar();	
});

$(document).on('click', '.export', function () {
    var idCondutor = $(this).attr('id');
	
	let get = window.location.search;
	get = get.replace('condutor%2Fcontrole-financeiro', 'condutor/export-folha-ponto');
    window.open(get + '&idCondutor=' + idCondutor)
	
});

function gerenciadorPdf(){
        let get = window.location.search;

        get = get.replace('condutor%2Fcontrole-financeiro', 'condutor/report-financeiro-pdf');
        var keys = $('#w2').yiiGridView('getSelectedRows');
        console.log(keys)
        window.open(get + '&keys=' + JSON.stringify(keys))
    }

    function gerenciadorXls(){
        let get = window.location.search;

        get = get.replace('condutor%2Fcontrole-financeiro', 'condutor/report-financeiro-xls');
        var keys = $('#w2').yiiGridView('getSelectedRows');
        console.log(keys)
        window.open(get + '&keys=' + JSON.stringify(keys))
    }
    
    setInterval(() => {
        itens = $("#w4 li").remove();
        item = $("#w4 li")[itens.length-1];
        if($(item).prop('title') != 'Portable Document Format') {
            $("#w4").append('<li id="meuXls" title="Excel"><a onclick="gerenciadorXls()" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>')
            $("#w4").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')
        }
    }, 500);

    $('.diasTrabalhados').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.sabadoLetivo').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.diasExcepcionais1').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.viagemKm1').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.diasExcepcionais2').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.viagemKm2').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.protocoloTESC').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.protocoloGC').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.lote').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    // $('.saldoAF').change(function(val) {
    //     calculaValorNota();
    //     console.log($(this).attr('id'));
    // })

    function calculaValorNota(id)
    {
        var diasTrabalhados = $('#diasTrabalhados_' + id).val();
        var sabadoLetivo = $('#sabadoLetivo_' + id).val();
        var diasExcepcionais1 = $('#diasExcepcionais1_' + id).val();
        var viagemKm1 = $('#viagemKm1_' + id).val();
        var diasExcepcionais2 = $('#diasExcepcionais2_' + id).val();
        var viagemKm2 = $('#viagemKm2_' + id).val();
        var kmViagemAtual = $('#kmViagemAtual_' + id).val();
        var kmViagemSabadoLetivo = $('#kmViagemSabadoLetivo_' + id).val();
        var valorPago = $('#valorPago_' + id).val();
        
        var valorNota = (diasTrabalhados * valorPago * kmViagemAtual) + 
            (sabadoLetivo * valorPago * kmViagemSabadoLetivo) +
            (diasExcepcionais1 * viagemKm1 * valorPago) +
            (diasExcepcionais2 * viagemKm2 * valorPago);

        $('#valorNota_' + id).val(valorNota.toFixed(2));

        calculaSaldoAF(id);
    }

    function calculaSaldoAF(id)
    {
        var valorNota = $('#valorNota_' + id).val();
        
        var saldoAF = $('#saldoAFAnterior_' + id).val() - valorNota;

        $('#saldoAF_' + id).val(saldoAF.toFixed(2));
    }

    $('#saveBtn').click(function () {
        var data = [];

        $('.diasTrabalhados').each (function(index) {
            console.log($(this))
        })
        
        // $.post("index.php?r=condutor%2Fsave-historico-financeiro", data, function(result){
        //         console.log(result)
        //         console.log($(this).parent())
        //     });
        // }
    })

    $('.cloneBtn').click(function () {
        var tr = $(this).parent().parent();
        tr.after(tr.clone());
        console.log($(this).parent().parent());
    })

    $( "input[name='ControleFinanceiroSearch[diasTrabalhados]']" ).attr('type', 'number')
    function forceNumeric(){
        var $input = $(this);
        $input.val($input.val().replace(/[^\d]+/g,''));
    }
    $('body').on('input', 'input[type="number"]', forceNumeric);


$(document).on('click', '.ocorrencia', function () {
    var dataGravar = $(this).attr('id');
	Swal.fire({
		title: "Preencher as informações referentes a ocorrência.",			
		html: '<input id="oco"  class="swal2-input" placeholder="Ocorrência">',
		showCancelButton: true ,
		confirmButtonColor: 'green'
	}).then((result) => {				
		var oco =  document.getElementById('oco').value;
		if(oco){
			$.ajax({	
				type: 'POST',
				url: 'index.php?r=condutor/gravar-ocorrencia',
				data:{
					data: dataGravar,
					ocorrencia: oco,
				},
			}).done(function(data) {
				if(data == 1){				
					Swal.fire({
						width: '600px',
						title: 'Atenção usuário',
						text: "A ocorrência foi gravada",
						icon: 'warning',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ok',
					}).then((result) => {
						buscar();	
						

					});	
				}	
			});
		}
	});
});

$(document).on('click', '.excluir', function () {
    var dataApagar = $(this).attr('id');
	$.ajax({	
				type: 'POST',
				url: 'index.php?r=condutor/gravar-ocorrencia',
				data:{
					data: dataApagar,
					ocorrencia: '-',
				},
			}).done(function(data) {
				if(data == 1){				
					Swal.fire({
						width: '600px',
						title: 'Atenção usuário',
						text: "A ocorrência foi apagada",
						icon: 'warning',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ok',
					}).then((result) => {
						buscar();	
						location.reload()
					});	
				}	
			});
});

</script>