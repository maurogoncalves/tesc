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
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoTransporteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RENOVAÇÃO - ALUNOS ATENDIDOS';
$this->params['breadcrumbs'][] = $this->title;

$config = Configuracao::setup();
if($config->exibeRenovacao){
	
}
function mountSelect($camposOrdenacao,$index){
	$str = '';
	$option = '';
	foreach($camposOrdenacao as $key=>$value){
		$sel = '';
		if($index == $key){
			$sel = ' selected ';
		}
		$str .= '<option value="'.$key.'" '.$sel.' >'.$value.'</option>';
	} 	
	return $str;
}

if(empty($ra)){
	$ra = 0;
}

if(empty($idAluno)){
	$idAluno = 0;
}
	
?> 
<!--
<script src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css"></script> 
-->


<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
					<div class="col-md-6">
                    <h4>
                    <?= '<span class="label label-primary">Total: '.count($alunos).'</span>'; ?>
                    </h4>
					</div>
					<div class="col-md-6">
					<h4>
                    <?= '<span class="label label-primary" id="recarregar">Trazer todos os registros</span>'; ?>
                    </h4>
					</div>
					
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-body">
              <table id="example" class="table table-striped table-bordered" style="width:100%;font-size:8px!important">
        <thead >
            <tr>
                <th>Renovar <br> Benefício</th>
                <th>Motivo</th>
                <th>Nome</th>
                <th>RA</th>
				<th>Modalidade</th>
                <th>Atualizar <br> Endereço</th>
                <th>Período <br> Ensino</th>
				<th>Ano/Série <br> Turma</th>
				<th>Horário Entrada <br> Horário Saída</th>
				<th>Telefone</th>
				<th>Endereço </th>
				<th>Complemento </th>
				<th>Bairro </th>
				<th>Necessidades</th>
				<th>Validar <br> Dados</th>
				
            </tr>
        </thead>
        <tbody style=''>
			<?php foreach($alunos as $dados){
				if($dados['tem_outra_solicitacao'] == '0'){	
			?>
            <tr id='tr-id-<?=$dados['idAluno']?>'>
                <td>
				<input disabled class="renovar" type="radio" name='renovar-<?=$dados['idAluno']?>' id='renovar-1-<?=$dados['idAluno']?>-<?=$dados['id']?>' style="margin-left:5px;margin-right:8px;margin-bottom:10px;" />Sim
				<br>
				<input disabled class="renovar" type="radio" name='renovar-<?=$dados['idAluno']?>' id='renovar-2-<?=$dados['idAluno']?>-<?=$dados['id']?>' style="margin-left:5px;margin-right:8px;margin-bottom:10px;" />Não
				</td>
                <td><?php
					$options = mountSelect(SolicitacaoTransporte::MOTIVO_RENOVACAO,0);
					print "<select class='form-control motivo' style='display:none;height:25px!important;width:90px!important;font-size:8px!important'  id='motivo_renova-".$dados['idAluno']."' name='motivo_renova-".$dados['idAluno']."' >'".$options."</select>";												
				?></td>
                <td><?=$dados['nome']?></td>
                <td>
				<input type="hidden" id="ra-<?=$dados['idAluno']?>" name="ra-<?=$dados['idAluno']?>" value="<?=$dados['idAluno']?>-<?=$dados['id']?>" >
				<?=$dados['RA'].' '.$dados['RAdigito']?>
				</td>
				<td>
				<?php 
				$modalidade = Aluno::ARRAY_MODALIDADE[$dados['modalidadeBeneficio']];				
				if($dados['modalidadeBeneficio'] == 1 ){
					if(empty($dados['necessidades'])){
						echo$modalidade.' <br> COMUM';
					}else{
						echo$modalidade.' <br> ADAPTADO';
					}
				}else{
					echo$modalidade;
				}				
				?>
				</td>
                <td>
				<input class="atualizar_end" type="radio" disabled name='atualizar_end-<?=$dados['idAluno']?>' id='1-<?=$dados['idAluno']?>' style="margin-left:5px;margin-right:8px;margin-bottom:10px;" />Sim
				<br>
				<input class="atualizar_end" type="radio" checked disabled name='atualizar_end-<?=$dados['idAluno']?>' id='2-<?=$dados['idAluno']?>' style="margin-left:5px;margin-right:8px;margin-bottom:10px;" />Não
				</td>
                <td><?php
					$options = mountSelect(aluno::ARRAY_TURNO,$dados['turno']);
					print "<select disabled class='form-control turno' style='height:25px!important;width:130px!important;font-size:8px!important' id='turno-".$dados['idAluno']."' name='turno-".$dados['idAluno']."' >'".$options."</select>";												
					echo'<br>';
					$options = mountSelect(Escola::ARRAY_ENSINO,$dados['ensino']);
					print "<select disabled class='form-control ensino' style='height:25px!important;width:130px!important;font-size:8px!important' id='ensino-".$dados['idAluno']."' name='ensino-".$dados['idAluno']."' >'".$options."</select>";												
				?>
				</td><td>
				<?php
					$options = mountSelect(Aluno::ARRAY_SERIES,$dados['serie']);
					print "<select disabled class='form-control serie' style='height:25px!important;width:70px!important;font-size:8px!important' id='serie-".$dados['idAluno']."' name='serie-".$dados['idAluno']."' >'".$options."</select>";												
					echo'<br>';
					$options = mountSelect(Aluno::ARRAY_TURMA,$dados['turma']);
					print "<select disabled class='form-control turma' style='height:25px!important;width:70px!important;font-size:8px!important' id='turma-".$dados['idAluno']."' name='turma-".$dados['idAluno']."' >'".$options."</select>";												
				?></td>
				<td><input disabled type="time" id="entrada-<?=$dados['idAluno']?>" style='height:25px!important;width:77px!important;font-size:8px!important' class="form-control entrada" name="Aluno[horarioEntrada]" value="<?=$dados['horarioEntrada']?>" aria-required="true" aria-invalid="false">
				<br>
				<input disabled type="time" id="saida-<?=$dados['idAluno']?>" style='height:25px!important;width:77px!important;font-size:8px!important' class="form-control saida" name="Aluno[horarioSaida]" value="<?=$dados['horarioSaida']?>" aria-required="true" aria-invalid="false"></td>
				<td><input disabled type="text" id="telefone-<?=$dados['idAluno']?>" style='height:25px!important;width:85px!important;font-size:8px!important' class="form-control telefone" name="Aluno[telefoneResidencial]" value="<?=$dados['telefoneResidencial']?>" maxlength="15" onblur="MascaraTelefone(this);" onkeypress="MascaraTelefone(this);" aria-invalid="false">				</td>
				<td><?=$dados['endereco']?>, <?=$dados['numeroResidencia']?></td>
				<td><?=$dados['complementoResidencia']?></td>
				<td><?=$dados['bairro']?></td>
				<td><?=$dados['necessidades']?></td>
				<td>
				<i class="salvar glyphicon glyphicon-ok-sign" id="salvar-<?=$dados['idAluno']?>-<?=$dados['id']?>" style='cursor: pointer; font-size:16px;!important;color:#00FF7F'></i> 
				</td>				
            </tr>            
			<?php 
				} //fim if
			}	//fim foreach
			
			?>
        </tbody>
        
    </table>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">



$(document).ready(function() {
	var exibeRenovacao = <?php echo $config->exibeRenovacao?>;	
	if(exibeRenovacao == 0 ){
		Swal.fire({
			title: '😊 Atenção usuário(a)!',
			text: "A tela de renovação ainda não está disponivel para ser usada, aguarde o supervisor liberar. Obrigado",
			icon: 'warning',
			showCancelButton: false,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
        confirmButtonText: 'Ok, vou aguardar',
		}).then((result) => {
			window.location.href = 'index.php?r=aviso/meus-avisos';
		});
	
		
   }
	$(".renovar").prop('disabled', false);	
	
	var ra = <?php echo $ra?>;
	var idAluno = <?php echo $idAluno?>;
	if(ra != 0){
		var id = $("#ra-"+idAluno).val();		
		if (typeof id === "undefined") { 
		
			Swal.fire({
				title: 'Atenção usuário(a)!',
				text: "Algum problema aconteceu com esse aluno ao tentar atualizar o endereço",
				icon: 'warning',
				showCancelButton: false,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Ok',
			}).then((result) => {
				window.location.href = 'index.php?r=renovacao/index';
			});	
			
			
		}else{
			var arr = id.split('-');		
			$("#renovar-1-"+arr[0]+"-"+arr[1]).prop('checked', true);
			$("#1-"+arr[0]).prop('checked', true);
			$("#motivo_renova-"+arr[0]).show();
			$("#motivo_renova-"+arr[0]).val(7);
			$("#turno-"+arr[0]).prop('disabled', false);
			$("#ensino-"+arr[0]).prop('disabled', false);
			$("#serie-"+arr[0]).prop('disabled', false);
			$("#turma-"+arr[0]).prop('disabled', false);
			$("#entrada-"+arr[0]).prop('disabled', false);
			$("#saida-"+arr[0]).prop('disabled', false);
			$("#telefone-"+arr[0]).prop('disabled', false);	
			$("#1-"+arr[0]).prop('disabled', false);
			$("#2-"+arr[0]).prop('disabled', false);
		}
		
	}
	
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
		"lengthMenu": [[10, 50, 100, 200], [10, 50, 100, 200, "Todos"]],
		 "search": {
			"search": "<?php echo $ra == 0 ? "" : $ra;?>"
		  }
	});
	
	
});

$(document).on('click', '.renovar', function () {
	
    var id = $(this).attr('id');
	var arr = id.split('-');
	$("#motivo_renova-"+arr[2]).show();
	if(arr[1] == 2){	
	
		$("#motivo_renova-"+arr[2]).find('[value="0"]').remove();
		$("#motivo_renova-"+arr[2]).find('[value="7"]').remove();
		$("#motivo_renova-"+arr[2]).find('[value="8"]').remove();

		$("#motivo_renova-"+arr[2]).val(0);
		$("#turno-"+arr[2]).prop('disabled', true);
		$("#ensino-"+arr[2]).prop('disabled', true);
		$("#serie-"+arr[2]).prop('disabled', true);
		$("#turma-"+arr[2]).prop('disabled', true);
		$("#entrada-"+arr[2]).prop('disabled', true);
		$("#saida-"+arr[2]).prop('disabled', true);
		$("#telefone-"+arr[2]).prop('disabled', true);	
		$("#1-"+arr[2]).prop('disabled', true);
		$("#2-"+arr[2]).prop('disabled', true);
	}else{
		$("#motivo_renova-"+arr[2]).find('[value="0"]').remove();		
		$("#motivo_renova-"+arr[2]).find('[value="8"]').remove();

		$("#turno-"+arr[2]).prop('disabled', false);
		$("#ensino-"+arr[2]).prop('disabled', false);
		$("#serie-"+arr[2]).prop('disabled', false);
		$("#turma-"+arr[2]).prop('disabled', false);
		$("#entrada-"+arr[2]).prop('disabled', false);
		$("#saida-"+arr[2]).prop('disabled', false);
		$("#telefone-"+arr[2]).prop('disabled', false);		
		$("#motivo_renova-"+arr[2]).val(7);	
		$("#1-"+arr[2]).prop('disabled', false);
		$("#2-"+arr[2]).prop('disabled', false);	
	}

});

$(document).on('click', '.salvar', function () {
    var id = $(this).attr('id');
	var arr = id.split('-');
	
	var renovarS = $("#renovar-1-"+arr[1]+"-"+arr[2]).is(":checked");
	var renovarN = $("#renovar-2-"+arr[1]+"-"+arr[2]).is(":checked");

	if((renovarS == false) && (renovarN == false)){		
		Swal.fire({
			title: 'Atenção usuário(a)!',
			text: "Escolha uma opção de renovar",
			icon: 'warning',
			showCancelButton: false,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Ok',
		}).then((result) => {
			console.log('OK VOU VERIFICAR')
		});	

	}else{
		var ra = <?php echo $ra?>;
		if(ra != 0){
			var atualizar_end = 1;			
		}else{
			var atualizar_end = 2;		
		}	
		
		var id_aluno = arr[1];

		var id_solicitacao = arr[2];
		var motivo_renova = $("#motivo_renova-"+arr[1]).val();		
		
		var turno = $("#turno-"+arr[1]).val();		
		var ensino = $("#ensino-"+arr[1]).val();		
		var serie = $("#serie-"+arr[1]).val();		
		var turma = $("#turma-"+arr[1]).val();		
		var entrada = $("#entrada-"+arr[1]).val();		
		var saida = $("#saida-"+arr[1]).val();		
		var telefone = $("#telefone-"+arr[1]).val();
		var renovar = $("#renovar-"+arr[1]+"-"+arr[2]).val();	
		
		if(renovarS){
			Swal.fire({
            title: 'Validar os dados?',
            text: "Deseja Validar os Dados?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO'
          }).then((result) => {			  
			if(result.value){
				$.ajax({	
				type: 'POST',
				url: 'index.php?r=renovacao/salvar',
				data:{
					id_aluno: id_aluno,
					id_solicitacao: id_solicitacao,
					motivo_renova: motivo_renova,
					atualizar_end: atualizar_end,
					turno: turno,
					ensino: ensino,
					serie: serie,
					turma: turma,
					entrada: entrada,
					saida: saida,
					telefone: telefone,
				  renovar: 'S',
				},
			}).done(function(data) {
					switch (data) {
					  case '1':		
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "1 - O benefício foi renovado, não houve alteração de período ou endereço.",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							console.log('OK')
						});
						break;
					  case '2':
					    Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "2 - O benefício na modalidade frete foi renovado com status atendido",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							console.log('OK')
						});
						break;
					  case '3':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "3 - O benefício na modalidade frete foi solicitado com status recebido porque o endereço foi alterado",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index/';
						});
						break;
					  case '5':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "5 - O benefício na modalidade passe foi renovado com status concedido",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index/';
						});
						break;		
					  case '6':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "6 - O benefício na modalidade passe foi renovado com status concedido, e o endereço e/ou o periodo foram alterados",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index/';
						});
						break;	
					  case '7':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "7 - O benefício na modalidade frete foi solicitado com status recebido, não existe condutor para colocar o aluno",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;		
					  case '8':
   						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "8 - O benefício na modalidade frete foi solicitado com status recebido, não existe o veículo do condutor para colocar o aluno",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;				
					   case '9':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "9 - O benefício na modalidade frete foi solicitado com status recebido, pois o condutor que já o atendia, não atende mais o período desejado",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;			
					  case '10':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "10 - O Benefício na modalidade frete foi renovado com sucesso",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;		
					  case '12':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "12 - O Benefício na modalidade frete foi renovado com sucesso",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;		
					 case '13':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "13 - Favor revisar o cadastro do aluno, os números de documentos devem ser únicos",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index';
						});					    
						break;				
					  default:
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "Algum problema aconteceu durante a renovação. Tente novamente mais tarde ou verifique os dados do aluno",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#FF0000',
							cancelButtonColor: '#FF0000',
							confirmButtonText: 'Ok',
						}).then((result) => {
							  console.log('OK VOU VERIFICAR')
						});
					}
					
					$("#renovar-1-"+arr[1]+"-"+arr[2]).prop('checked', false);					
					$("#renovar-2-"+arr[1]+"-"+arr[2]).prop('checked', false);		
					$("#renovar-1-"+arr[1]+"-"+arr[2]).prop('disabled', true);	
					$("#renovar-2-"+arr[1]+"-"+arr[2]).prop('disabled', true);	
					$("#motivo_renova-"+arr[1]).hide();	
					$("#1-"+arr[1]).prop('disabled', true);	
					$("#2-"+arr[1]).prop('disabled', true);	
					$("#turno-"+arr[1]).prop('disabled', true);
					$("#ensino-"+arr[1]).prop('disabled', true);
					$("#serie-"+arr[1]).prop('disabled', true);
					$("#turma-"+arr[1]).prop('disabled', true);
					$("#entrada-"+arr[1]).prop('disabled', true);
					$("#saida-"+arr[1]).prop('disabled', true);
					$("#telefone-"+arr[1]).prop('disabled', true);	
					$("#1-"+arr[1]).prop('disabled', true);
					$("#2-"+arr[1]).prop('disabled', true);	
					$('#tr-id-'+arr[1]).remove();	

					
					
				});
				
			}else if(result.dismiss == 'cancel'){
				console.log('cancel');
			}
		
			
          });

			
		
			
			
		}else{
			if(motivo_renova == null){
				Swal.fire({
					title: 'Atenção usuário(a)!',
					text: "Escolha uma opção na coluna Motivo.",
					icon: 'warning',
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ok',
				}).then((result) => {
					console.log('OK VOU VERIFICAR')
				});	
			}else{
				$.ajax({	
						type: 'POST',
						url: 'index.php?r=renovacao/salvar',
						data:{
						  id_aluno: id_aluno,
						  id_solicitacao: id_solicitacao,
						  motivo_renova: motivo_renova,
						  atualizar_end: atualizar_end,
						  turno: turno,
						  ensino: ensino,
						  serie: serie,
						  turma: turma,
						  entrada: entrada,
						  saida: saida,
						  telefone: telefone,
						  renovar: 'N',
						},
				}).done(function(data) {					
					  switch (data) {
					  case '1':		
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "1 - O benefício foi renovado, não houve alteração de período ou endereço.",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							console.log('OK')
						});
						break;
					  case '2':
					    Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "2 - O benefício na modalidade frete foi renovado com status atendido",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							console.log('OK')
						});
						break;
					  case '3':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "3 - O benefício na modalidade frete foi solicitado com status recebido porque o endereço foi alterado",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index/';
						});
						break;
					  case '5':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "5 - O benefício na modalidade passe foi renovado com status concedido",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index/';
						});
						break;		
					  case '6':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "6 - O benefício na modalidade passe foi renovado com status concedido, e o endereço e/ou o periodo foram alterados",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index/';
						});
						break;	
					  case '7':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "7 - O benefício na modalidade frete foi solicitado com status recebido, não existe condutor para colocar o aluno",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;		
					  case '8':
   						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "8 - O benefício na modalidade frete foi solicitado com status recebido, não existe o veículo do condutor para colocar o aluno",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;				
					   case '9':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "9 - O benefício na modalidade frete foi solicitado com status recebido, pois o condutor que já o atendia, não atende mais o período desejado",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;			
					  case '10':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "10 - O Benefício na modalidade frete foi renovado com sucess",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;	
					 case '11':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "11 - O benefício não foi renovado, a solicitação foi encerrada",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
					 break;	
					  case '12':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "12 - O Benefício na modalidade frete foi renovado com sucess",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 console.log('OK VOU VERIFICAR')
						});
						break;		
					 case '13':
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "13 - Favor revisar o cadastro do aluno, os números de documentos devem ser únicos",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok',
						}).then((result) => {
							 window.location.href = 'index.php?r=renovacao/index';
						});					    
						break;				
					  default:
						Swal.fire({
							title: 'Atenção usuário(a)!',
							text: "Algum problema aconteceu durante a renovação. Tente novamente mais tarde ou verifique os dados do aluno",
							icon: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#FF0000',
							cancelButtonColor: '#FF0000',
							confirmButtonText: 'Ok',
						}).then((result) => {
							  console.log('OK VOU VERIFICAR')
						});
					}
					
					$("#renovar-1-"+arr[1]+"-"+arr[2]).prop('checked', false);					
					$("#renovar-2-"+arr[1]+"-"+arr[2]).prop('checked', false);					
					$("#renovar-1"+arr[1]+"-"+arr[2]).prop('disabled', true);	
					$("#renovar-2"+arr[1]+"-"+arr[2]).prop('disabled', true);	
					$("#motivo_renova-"+arr[1]).hide();	
					$("#1-"+arr[1]).prop('disabled', true);	
					$("#2-"+arr[1]).prop('disabled', true);	
					$("#turno-"+arr[1]).prop('disabled', true);
					$("#ensino-"+arr[1]).prop('disabled', true);
					$("#serie-"+arr[1]).prop('disabled', true);
					$("#turma-"+arr[1]).prop('disabled', true);
					$("#entrada-"+arr[1]).prop('disabled', true);
					$("#saida-"+arr[1]).prop('disabled', true);
					$("#telefone-"+arr[1]).prop('disabled', true);	
					$("#1-"+arr[1]).prop('disabled', true);
					$("#2-"+arr[1]).prop('disabled', true);		

					$('#tr-id-'+arr[1]).remove();	
				});
			}
			
		}
		
	}
	

});

$(document).on('click', '.atualizar_end', function () {
    var id = $(this).attr('id');
	var arr = id.split('-');
		
	if(arr[0] == 1){		
		$("#turno-"+arr[1]).prop('disabled', true);
		$("#ensino-"+arr[1]).prop('disabled', true);
		$("#serie-"+arr[1]).prop('disabled', true);
		$("#turma-"+arr[1]).prop('disabled', true);
		$("#entrada-"+arr[1]).prop('disabled', true);
		$("#saida-"+arr[1]).prop('disabled', true);
		$("#telefone-"+arr[1]).prop('disabled', true);	
		
		Swal.fire({
            title: 'Atenção usuário(a)!',
            text: "Você será redirecionada(o) para a tela de cadastro do aluno, para atualizar o endereço",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO'
        }).then((result) => {			  
			if(result.value){
				window.location.href = 'index.php?r=aluno/update&id='+arr[1]+'&redirect=1';				
			}else if(result.dismiss == 'cancel'){
				console.log('cancel');
			}
        });
			
			
	}
});

$(document).on('click', '#recarregar', function () {
   window.location.href = 'index.php?r=renovacao/index';
});

</script>

