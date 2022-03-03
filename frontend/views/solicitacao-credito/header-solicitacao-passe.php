<?php
use common\models\ReciboPagamentoAutonomo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\SolicitacaoCredito;

function td($str)
{
    return '<td>' . $str . '</td>';
}


?>


<?= $this->render('dados-iniciais', ['model' => $model, 'configuracao' => $configuracao]) ?>

<div class="row mt-10">
	<div class="col-md-12">
	<table class="table table-striped table-bordered">
		<tr style='text-align:center;font-weight:bold;background-color:#fff!important'> 
			<td>Dias letivos do mês </td>
			<td>Valor Necessário Total Mensal </td>
			<td>Valor Necessário Aluno </td>
			<td>Saldo Restante na Escola </td>
			<td>Dias Letivos Restantes </td>
			<td>Saldo Restante nos Cartões </td>
			<td>Valor a ser Creditado </td>
		</tr>	
		<tr style='background-color:#fff!important'> 
			<td><select name="diasLetivosMes" id="diasLetivosMes"   class="form-control mt-15">
                <?php 
                    for($i = 1; $i < 31; $i++) {
						if($solCred[0]['diasLetivosMes'] == $i){
							print '<option selected value='.$i.'>'.$i.'</option>';	
						}else{
							print '<option value='.$i.'>'.$i.'</option>';
						}                        
                    } 
                ?>
            </select> </td>
			<td><input name="valorNecessarioTotal" type="text" id="valorNecessarioTotal" value='<?= $solCred[0]['valorNecessarioTotal'] ? $solCred[0]['valorNecessarioTotal'] : '0'; ?>'  readonly="true" class="form-control mt-15"> </td>
			<td>
			<input name="valorNecessarioAluno" type="text" id="valorNecessarioAluno"  readonly="true" class="form-control mt-15">
			<input name="valorNecessarioTotalAux" type="text" id="valorNecessarioTotalAux"  readonly="true" class="form-control mt-15"> 
			</td>
			<td><input name="saldoRestanteEscola" type="text" id="saldoRestanteEscola" class="form-control mt-15 money" value='<?= $solCred[0]['saldoRestante'] ? $solCred[0]['saldoRestante'] : '0'; ?>'> </td>
			<td> <select name="diasLetivosRestantes" id="diasLetivosRestantes" class="form-control mt-15"  >
                <?php 
                    for($i = 0; $i < 11; $i++) {
						if($solCred[0]['diasLetivosRestantes'] == $i){
							print '<option selected value='.$i.'>'.$i.'</option>';
						}else{
							print '<option value='.$i.'>'.$i.'</option>';
						}                        
                    } 
                ?>
            </select> </td>
			<td><input type="text" name="saldoRestanteCartoes" id="saldoRestanteCartoes" readonly="true" class="form-control mt-15" value=''> </td>
			<td><input type="text" name="valorCreditado" id="valorCreditado" readonly="true" class="form-control mt-15" value='<?= $solCred[0]['valorCreditado'] ?>'> </td>
		</tr>	
	</table>
	</div>   
</div>