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
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Dias letivos do mês</label>
            <select name="diasLetivosMes" id="diasLetivosMes"  name="diasLetivosMes" class="form-control mt-15">
                <?php 
                    for($i = 1; $i < 31; $i++) {
                        print '<option value='.$i.'>'.$i.'</option>';
                    } 
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Valor Necessário</label>
            <input name="valorNecessarioTotal" type="text" id="valorNecessarioTotal" readonly="true" class="form-control mt-15">
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Saldo Restante na Escola</label>
            <input name="saldoRestanteEscola" type="text" id="saldoRestanteEscola" class="form-control mt-15 money">
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Dias Letivos Restantes</label>
            <select name="diasLetivosRestantes" id="diasLetivosRestantes" class="form-control mt-15">
                <?php 
                    for($i = 0; $i < 11; $i++) {
                        print '<option value='.$i.'>'.$i.'</option>';
                    } 
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Saldo Restante nos Cartões</label>
            <input type="text" name="saldoRestanteCartoes" id="saldoRestanteCartoes" readonly="true" class="form-control mt-15">
        </div>
    </div>
    <div class="col-md-2">
        <div class="box box-solid p-10">
            <label class="control-label">Valor a ser Creditado</label>
            <input type="text" name="valorCreditado" id="valorCreditado" readonly="true" class="form-control mt-15">
        </div>
    </div>
</div>