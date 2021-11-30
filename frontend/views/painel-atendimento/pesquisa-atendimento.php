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
							<div class="col-md-3">
								<span><br><br></span>
								<?= Html::submitButton('Filtrar', ['class' => 'btn btn-primary pull-left']) ?>	
							</div>
							<div class="col-md-3">
									<span><br><br></span>
								<div class="btn-group pull-right" >
								<button id="w55" class="btn btn-default dropdown-toggle " title="Exportar" data-toggle="dropdown" aria-expanded="false" style="color:#3980D8">Exportar dados  <i class="glyphicon glyphicon-cloud-download"></i>  </button>
								<ul id="w66" class="dropdown-menu dropdown-menu-right">
									<li title="TEXT"><a class="export-txt"  tabindex="-1"  onclick='gerenciarExportacao(event,"TXT")'><i class="text-muted glyphicon glyphicon-floppy-save"></i> TXT</a></li>
									<li title="CSV"><a class="export-csv"  tabindex="-1"  onclick='gerenciarExportacao(event,"CSV")'><i class="text-primary glyphicon glyphicon-floppy-open"></i> CSV</a></li>
									<li title="Microsoft Excel 95+"><a class="export-xls" onclick='gerenciarExportacao(event,"EXCEL")' data-mime="application/vnd.ms-excel" data-hash="c78def80d35ad515b4ececb6260d2a82230d11149b73a853a3f74d8ea62c7dfcgridviewexportar-listagemapplication/vnd.ms-excelutf-81{&quot;worksheet&quot;:&quot;ExportarPlanilha&quot;,&quot;cssFile&quot;:&quot;&quot;}" data-css-styles="{&quot;.kv-group-even&quot;:{&quot;background-color&quot;:&quot;#f0f1ff&quot;},&quot;.kv-group-odd&quot;:{&quot;background-color&quot;:&quot;#f9fcff&quot;},&quot;.kv-grouped-row&quot;:{&quot;background-color&quot;:&quot;#fff0f5&quot;,&quot;font-size&quot;:&quot;1.3em&quot;,&quot;padding&quot;:&quot;10px&quot;},&quot;.kv-table-caption&quot;:{&quot;border&quot;:&quot;1px solid #ddd&quot;,&quot;border-bottom&quot;:&quot;none&quot;,&quot;font-size&quot;:&quot;1.5em&quot;,&quot;padding&quot;:&quot;8px&quot;},&quot;.kv-table-footer&quot;:{&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-page-summary td&quot;:{&quot;background-color&quot;:&quot;#ffeeba&quot;,&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-align-center&quot;:{&quot;text-align&quot;:&quot;center&quot;},&quot;.kv-align-left&quot;:{&quot;text-align&quot;:&quot;left&quot;},&quot;.kv-align-right&quot;:{&quot;text-align&quot;:&quot;right&quot;},&quot;.kv-align-top&quot;:{&quot;vertical-align&quot;:&quot;top&quot;},&quot;.kv-align-bottom&quot;:{&quot;vertical-align&quot;:&quot;bottom&quot;},&quot;.kv-align-middle&quot;:{&quot;vertical-align&quot;:&quot;middle&quot;},&quot;.kv-editable-link&quot;:{&quot;color&quot;:&quot;#428bca&quot;,&quot;text-decoration&quot;:&quot;none&quot;,&quot;background&quot;:&quot;none&quot;,&quot;border&quot;:&quot;none&quot;,&quot;border-bottom&quot;:&quot;1px dashed&quot;,&quot;margin&quot;:&quot;0&quot;,&quot;padding&quot;:&quot;2px 1px&quot;}}" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>
									<li title="Portable Document Format"><a class="export-pdf"  tabindex="-1"  onclick='gerenciarExportacao(event,"PDF")'><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>
								</ul>
								</div>
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
					 <table id="example" class="table table-striped table-bordered" style="width:100%;font-size:12px!important">
        <thead >
            <tr>
                <th>Id</th>
                <th>Aluno</th>
                <th>RA</th>
				<th>Data</th>
				<th>Entrada</th>
				<th>Saída</th>
				<th>Ano/Série e Turma</th>
				<th>Endereço </th>
				<th>Bairro </th>
				<th>Escola </th>
				<th>Condutor </th>
				<th>Período da Consulta </th>
				
            </tr>
        </thead>
        <tbody style=''>
			<?php 
			foreach($historicos as $dado){?>
			 <tr>
				<td><?=$dado['id_aluno']?></td>
				<td><?=$dado['aluno']?></td>
				<td><?=$dado['RA'].' '.$dado['RAdigito']?></td>
				<td><?=$dado['criacao']?></td>
				<td><?=$dado['horarioEntrada']?></td>
				<td><?=$dado['horarioSaida']?></td>
				<td><?=Aluno::ARRAY_SERIES[$dado['serie']].'/'.Aluno::ARRAY_TURMA[$dado['turma']]?></td>
				<td><?=$dado['tipoLogradouro'].' '.$dado['endereco'].', '.$dado['numeroResidencia']?></td>
				<td><?=$dado['bairro']?></td>
				<td><?=$dado['escola']?></td>
				<td><?=$dado['condutor']?></td>
				<td><?=$periodo?></td>
			 </tr>
			 <?php }//fim foreach	?>
        </tbody>
        
    </table>
				</div>
				</div>
			</section>
		</div>


<script type="text/javascript">



$(document).ready(function() {
		
	$('#example').DataTable( {
		"oLanguage": {
			"sSearch": "Pesquisar",
			"sLengthMenu": "Mostrando _MENU_ registros por página",
			 "sZeroRecords": "Nada Encontrado",
			 "sInfo": "Mostrando _START_ to _END_ of _TOTAL_ registros",
			 "sInfoEmpty": "Mostrando 0 to 0 of 0 records",
			 "sInfoFiltered": "(Filtrou de _MAX_ total registros)",
			 "oPaginate": {
				"sFirst":    "Primeiro",
				"sLast":    "Último",
				"sNext":    "Próximo",
				"sPrevious": "Anterior"
			},
		},
		"order": [[ 1, "asc" ]],
		"lengthMenu": [[25, 50, 100, 200], [25, 50, 100, 200, "Todos"]],
		 
	});
	
	
});


function gerenciarExportacao(event, tipo){
		
		event.preventDefault();
		
		Swal.fire({
            title: 'Exportar registros',
            text: "Confirma a exportação dos dados",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO'
          }).then((result) => {
            if (result.value) {
			  if(tipo == 'PDF'){
				 window.open('index.php?r=painel-atendimento/report-pesquisa-atendimento-pdf&tipo='+tipo) 
			  }else{
				  window.open('index.php?r=painel-atendimento/report-pesquisa-atendimento&tipo='+tipo)
			  }
			  
            }
          })


	}


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


</script>

