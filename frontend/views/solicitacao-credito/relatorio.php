<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use common\models\SolicitacaoCredito;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoCreditoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitação: '.$model->id;
$this->params['breadcrumbs'][] = $this->title;
function td($str){
	return '<td>'.$str.'</td>';
}
?>
<style type="text/css">
	input[type=checkbox]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(2); /* IE */
  -moz-transform: scale(2); /* FF */
  -webkit-transform: scale(2); /* Safari and Chrome */
  -o-transform: scale(2); /* Opera */
  padding: 10px;
  margin-top:0px;
}
a {
    cursor: pointer;    
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
              <h3><?= Html::encode($this->title) ?></h3>
              <h4>Escola: <?= Html::encode($model->escola->nome) ?></h4>
              <h4>Período: <?=  $model->inicio ? date("d/m/Y", strtotime($model->inicio)) : ''; ?> - <?=  $model->fim ? date("d/m/Y", strtotime($model->fim)) : ''; ?></h4>
              <h4>Status: <?= SolicitacaoCredito::ARRAY_STATUS[$model->status]; ?></h4>
              <?php 
                if(!$model->escola->calendario){ ?>
                      <div class="alert alert-light" role="alert">
                        Atenção: Esse tipo de escola não possui um calendário escolar. Entre em contato com o suporte.
                        Para fins de cálculo será aplicado os dias da semana (Seg-Sex).
                      </div>
                <?php } ?>
          </div>
        <?php $form = ActiveForm::begin(); ?> 
        <div class="box-body">
            <h3>Passe escolar</h3>
        	<div class="row">
        		<div class="col-md-12">
        			<table class="table table-hover table-striped table-bordered">
        				<thead>
        				<tr>
        					<th>Benefício</th>
        					<th>Nome</th>
        					<th>Passe escolar</th>
        					<th>Saldo</th>
        					<th>Justificativa</th>
        				</tr>
    					</thead>
    					<tbody>
        				<?php 
        				 $form = ActiveForm::begin([
    				          'encodeErrorSummary' => false,
    				          'errorSummaryCssClass' => 'help-block',
    				    ]);
                if($alunos){
          				foreach ($alunos as $aluno) {
          					  print '<tr class="aluno-linha">';
                      print '<input type="hidden" class="id-aluno" value="'.$aluno->id.'" name="alunoPasseEscolar[]" />';
          						print'<td align="center"><div class="checkbox"><label ><input name="CheckboxPasseEscolar['.$aluno->id.']" type="checkbox"  checked></label></div></td>';
          						print td('<span class="aluno">'.$aluno->nome.'</span>');
          						print td('<input class="form-control  passe passeEscolar" type="text" name="passeEscolar['.$aluno->id.'][Valor]" value="'.$aluno->passeEscolar.'" readonly="true">');
                      print td('<input class="form-control money passe saldo" type="text" name="passeEscolar['.$aluno->id.'][Saldo]" value="0">');
          					  print td('<input class="form-control" type="text" name="passeEscolar['.$aluno->id.'][Justificativa]" value="">');
                      print td('<a class="btn btn-primary pull-right consultarCredito"><i class="fas fa-search"></i></a>');
                      //print td('<a class="">Consultar</a> ');

          					print '</tr>';
          				}
                } else {
                  print '<tr><td colspan="5">Nenhum aluno com benefício ativo.</td></tr>';
                } 

        				ActiveForm::end();
        				?>
        				</tbody>
        			</table>
        		</div>
        	</div>
            <h3>Vale transporte</h3>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover table-striped table-bordered" >
                        <thead>
                        <tr>
                            <th>Benefício</th>
                            <th>Nome</th>
                            <th>Vale transporte</th>
                            <th>Saldo</th>
                            <th>Justificativa</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($alunosValeTransporte){
                              foreach ($alunosValeTransporte as $aluno) {
                                print '<tr class="aluno-linha">';
                                print '<input type="hidden" class="id-aluno" value="'.$aluno->id.'" name="alunoValeTransporte[]" />';
                                print'<td align="center"><div class="checkbox"><label ><input name="CheckboxValeTransporte['.$aluno->id.']" type="checkbox"  checked></label></div></td>';
                                print td('<span class="aluno">'.$aluno->nome.'</span>');
                                print td('<input class="form-control money" type="text" name="valeTransporte['.$aluno->id.'][Valor]" value="'.$aluno->valeTransporte.'">');
                                print td('<input class="form-control money" type="text" name="valeTransporte['.$aluno->id.'][Saldo]" value="">');
                                print td('<input class="form-control" type="text" name="valeTransporte['.$aluno->id.'][Justificativa]" value="">');
                                print td('<a class="btn btn-primary pull-right consultarCredito"><i class="fas fa-search"></i></a>');
                                print '</tr>';
                              } 
                            } else {
                              print '<tr><td colspan="5">Nenhum aluno com benefício ativo.</td></tr>';
                            }
                             ?>
                        </tbody>
                      </table>
                </div>
            </div> 
          <div class="row">
            <div class="col-md-3">
               <h3>Crédito administrativo</h3>

              <?php print  $model->status == SolicitacaoCredito::STATUS_EM_ANDAMENTO ? '<input  class="form-control money" value="" id="creditoAdministrativo" name="creditoAdministrativo" />' : $model->creditoAdministrativo; ?>
            </div>
          </div>  
          <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton('Salvar e Continuar' , ['class' => 'btn btn-primary pull-right' ])  ?>                 
            </div>
          </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
</div>




<div id="modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Informações do aluno</h4>
      </div>
      <div class="modal-body" id="informacoes">
        <div class="row">
            <div class="col-md-12">
                <label>CPF</label>
               <div class="input-group">
                  <input type="text" id="cpf" class="form-control">
                    <span class="input-group-btn">
                    <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                  </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Cartão de Vale Transporte</label>
               <div class="input-group">
                  <input type="text" id="cartaoValeTransporte" class="form-control">
                    <span class="input-group-btn">
                    <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                  </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Cartão de Passe Escolar</label>
               <div class="input-group">
                  <input type="text" id="cartaoPasseEscolar" class="form-control">
                    <span class="input-group-btn">
                    <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                  </span>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
      $('.copy').on("click", function(){
          let el = $(this).parent().parent().find('input');
          el.select();
          document.execCommand('copy');
      });
    
    $( ".consultarCredito" ).click(function() {
            console.log(this);
            let idAluno = $(this).parent().parent().find('.id-aluno').val();
            $.get('index.php?r=aluno/aluno-ajax', {id: idAluno}).done((result) => {
                console.log(result);   
                $('#modal').modal("show");
           
                $("#cpf").val(result.cpf)     
           });

        $.get('index.php?r=solicitacao-transporte/view-solicitacao-ajax', {id: idAluno}).done((result) => {
                console.log(result);   
                $('#modal').modal("show");
                $("#cartaoPasseEscolar").val(result.cartaoPasseEscolar)  
                $("#cartaoValeTransporte").val(result.cartaoValeTransporte)  
        });
    });
 
});
$("form").bind("keypress", function (e) {
    if (e.keyCode == 13) {

        return false;
    }
});
 // function converteMoedaFloat(valor){
 //      if(valor === ""){
 //         valor =  0;
 //      }else{
 //         valor = valor.replace(".","");
 //         valor = valor.replace(",",".");
 //         valor = parseFloat(valor);
 //      }
 //      return valor;
 //   }

	// function dinheiroBr(numero){
 //       var numero = numero.toFixed(2).split('.');
	//     numero[0] = "R$ " + numero[0].split(/(?=(?:...)*$)/).join('.');
	//     return numero.join(',');
	//}

	$('.checkbox').change(function() {
       	let check = $(this);
       	if(!check.find('input').is(':checked')){
       		check.parent().parent().css("background-color", "#cecece");
       		
       	} else {
       		check.parent().parent().css("background-color", "rgb(244, 244, 244)");
       	}
       
    });
    $( document ).ready(function() {
	$('.money').mask('#.##0,00', {reverse: true});
});
</script>


