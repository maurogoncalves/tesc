<?php
use common\models\SolicitacaoCredito;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use common\models\Escola;
use common\models\EscolaHomologacao;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

use common\models\Condutor;
use common\models\Configuracao;
use common\models\UsuarioGrupo;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoTransporteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitações Pendentes';
$this->params['breadcrumbs'][] = 'Solicitações Pendentes';
$content = 	 '<span class="label label-default" style="background-color:#1f7630;color:white; ">Total de solicitações do tipo Benefício: '.$totaisArr['SOLICITACOES_PENDENTES_BENEFICIO'].'</span>&nbsp;';
$content .=  '<span class="label label-default"  style="background-color:#185f26;color:white; ">Total de solicitações do tipo Cancelamento: '.$totaisArr['SOLICITACOES_PENDENTES_CANCELAMENTO'].'</span>&nbsp;';
$content .= '<span class="label label-default" style="background-color:#f39c12;color:white; ">Total de solicitações com status Deferido pelo Diretor: '.$totaisArr['SOLICITACOES_PENDENTES_DEFERIDO_DIRETOR'].'</span>&nbsp;';
$content .= '<span class="label label-default" style="background-color:#d78b13;color:white; ">Total de solicitações com status Andamento: '.$totaisArr['SOLICITACOES_PENDENTES_ANDAMENTO'].'</span>&nbsp;'; 
$content .= '<span class="label label-primary">Total Geral: '.$totaisArr['TOTAL'].'</span>'; 
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
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 style="text-align:center">
					<?= $content ?>
					</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
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
		          'allModels' => $escolas,
		          'sort' => [
		              'attributes' => [
						// 'nomeCompleto',
						// 'beneficio.andamento',
						// 'beneficio.diretor',
						// 'cancelamento.andamento',
						// 'cancelamento.diretor',
						// 'total'
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
                    // Cancelamento / Deferido pela DRE e Total;
                    'nomeCompleto', 
                    [
						'attribute' => 'beneficio.andamento',
						'label' => 'Benefício / Andamento',
						'value' => function($model) {
                            return $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['BENEFICIO'];
                        }
                    ],
                    [
						'attribute' => 'beneficio.diretor',
						'label' => 'Benefício / Deferido pelo Diretor',
						'value' => function($model) {
                            return $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['BENEFICIO'];
                        }
                    ],
                    [
						'attribute' => 'cancelamento.andamento',
						'label' => 'Cancelamento / Andamento',
						'value' => function($model) {
                            return $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['CANCELAMENTO'];
                        }
                    ],
                    [
						'attribute' => 'cancelamento.diretor',
						'label' => 'Cancelamento / Deferido pelo Diretor',
						'value' => function($model) {
                            return $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['CANCELAMENTO'];
                        }
					],
					[
						'attribute' => 'total',
						'label' => 'Total',
						'value' => function($model) {
							return 
									$GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['BENEFICIO'] +
									$GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['BENEFICIO'] + 
									$GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['CANCELAMENTO'] +
									$GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['CANCELAMENTO'];
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
    </div>
</div>
<script type="text/javascript">
    function clearInputs(e){
        e.stopPropagation();
        e.preventDefault();
        $('#solicitacaotransportesearch-ultimamovimentacao').val(''); 
        $('input[name="daterangepicker_start"]').val("");
        $('input[name="daterangepicker_end"]').val("");
                let params =window.location.href
             params = params.split('&')
            let finalLocation = ''
            for(let i=0; i<=params.length; i++){
                let param = params[i];
                if(param && param.startsWith('SolicitacaoTransporteSearch') && !param.startsWith('SolicitacaoTransporteSearch%5BultimaMovimentacao')){
                    finalLocation += '&'+param
                }
            }
            // console.warn(finalLocation)
            window.location.href = 'index.php?r=solicitacao-transporte%2Fsolicitacoes-aguardando-atendimento'+finalLocation;

    }

        function clearInputs2(e){
        e.stopPropagation();
        e.preventDefault();
        $('#solicitacaotransportesearch-data').val(''); 
        $('input[name="daterangepicker_start"]').val("");
        $('input[name="daterangepicker_end"]').val("");
                let params =window.location.href
             params = params.split('&')
            let finalLocation = ''
            for(let i=0; i<=params.length; i++){
                let param = params[i];
                if(param && param.startsWith('SolicitacaoTransporteSearch') && !param.startsWith('SolicitacaoTransporteSearch%5Bdata')){
                    finalLocation += '&'+param
                }
            }
            // console.warn(finalLocation)
            window.location.href = 'index.php?r=solicitacao-transporte%2Fsolicitacoes-aguardando-atendimento'+finalLocation;

    }

	$(document).ready(function() {
setTimeout(() => $("#w2").html($("#w66").html()), 200)

    
});
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
			  window.open('index.php?r=solicitacao-transporte/export-solicitacoes-pendentes&tipo='+tipo)
            }
          })


	}
setTimeout(() => {
    document.getElementById('w2').innerHTML= document.getElementById('w66').innerHTML
}, 200)
</script>
