<?php

use common\models\Aluno;
use common\models\SolicitacaoCredito;
use yii\widgets\ActiveForm;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoCreditoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitação: ' . $model->id;
$this->params['breadcrumbs'][] = $this->title;
// print_r($configuracao);
?>

<style>
    .debug {
        background-color: #D8544F;
        display: none;
    }
</style>
<?php $form = ActiveForm::begin([
    'id' => 'formCredito',
    
  ]); ?>
<?= $this->render('header-solicitacao-passe', ['model' => $model, 'configuracao' => $configuracao, 'solCred' => $solCred, 'temSolCred' => $temSolCred]) ?>
<div class="row mt-10">
	<div class="col-md-10">  </div>
	
	
	
	<div class="col-md-1"> 
	<p  class="btn btn-primary pull-right"  name='salvarProgresso' id="salvarProgresso">Salvar Progresso</p>
	</div>
	
	<div class="col-md-1"> 
	<input type='hidden' id='statusProgresso' name='statusProgresso' value='0'>
	<p  class="btn btn-danger pull-right"  name='salvar' id="salvar">Salvar e Finalizar</p>
	</div>
    <div class="col-md-12">
        <div class="box box-solid" >
            <h3><?=  $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? 'Passe Escolar' : 'Vale Transporte' ?></h3>
            <input type="checkbox" class="selecionarTodos" style="margin-left:5px;margin-right:8px;margin-bottom:10px;" />Selecionar todos
            <div class="pull-right" style="display: inline-flex;    align-items: center;">
                <form class="form-inline">
                    <b>Alunos atendidos:</b> 
                    <input type="text" class="form-control qtdeAlunos" id="qtdeAlunos" style="max-width: 50px;padding-right:15px;" readonly="true" value="0" />
                </form>
            </div>
            
            <table class="table table-responsive table-bordered">
                <tr>
                    <th>-</th>
                    <th>Benefício</th>
                    <?=  $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? '<th>FUNDHAS</th>': ''?>
                    <th>Nome</th>
                    <th>RA</th>
                    <th>Turma</th>
                    <th>Nº Cartão</th>
                    <th>Saldo em <?= date("d/m/Y", strtotime($model->criado)) ?></th>
                    <th class="debug">Dias letivos para fechar o mês (R$) e Saldo Descontado</th>
                    <th class="debug">Anti-UE</th>
                    <th class="debug">Saldo no Final do Mês</th>
					<!--
                    <th>Justificativa </th>
					-->
                    <th>Valor Necessário </th>
                    <th>Necessidade de crédito</th>
                    <th>&nbsp;</th>
                </tr>
                <?php $i = 0;
                foreach ($alunos as $aluno) : $i++;  ?>
                <tr>
                    <td class="center-td"><?= $i ?></td>
                    <td class="center-td"><input type="checkbox" id="<?= 'alunoMarcado-'.$aluno['idAl'] ?>" class="alunoMarcado" <?= $aluno['idSolicitacaoCredAl'] ? 'checked' : ''; ?> name="aluno[]" value="<?= $aluno['idAl'] ?>" /></td>
                    <?php if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR): ?>
                    <td class="center-td"><input type="checkbox" id="<?= 'fundhas-'.$aluno['idAl'] ?>" class="fundhas" <?= $aluno['fundhas'] ? 'checked' : ''; ?> name="fundhas[<?= $aluno['idAl']  ?>]" /></td>
                    <?php endif; ?>
                    <td class="center-text"><a target="_new"
                            href="<?= Url::toRoute(['aluno/view', 'id' =>  $aluno['idAl']]) ?>"><?= $aluno['nome'] ?></a></td>
                    <td class="center-text"><span data-toggle="tooltip"
                            title="AULA(S) NA SEMANA:<?php foreach ($aluno->alunoCurso as $alunoCurso) : echo ' ' . Aluno::ARRAY_DIAS_CURSO[$alunoCurso->dia]; endforeach; ?>"><?= $aluno['RA'].' '.$aluno['RAdigito'] ?></span></td>
                    <td class="center-text"><?= $aluno['turma'] ? Aluno::ARRAY_SERIES[$aluno['serie']].'/'.Aluno::ARRAY_TURMA[$aluno['turma']] : '-' ?></td>

                    <td class="center-text"><?= $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? $aluno['cartaoPasseEscolar'] : $aluno['cartaoValeTransporte'] ?></td>

                    <td class="center-text">
                        <input id="saldoRestante-<?= $aluno['idAl'] ?>" type="text" class="form-control money inputSaldoRestante"  value='<?= $aluno['saldo'] ? $aluno['saldo'] : '0'; ?>'  name="saldoRestante[<?= $aluno['idAl'] ?>]" >
						
                    </td>
                    
                    <td class="debug">
                        <input type="text" class="form-control inputDiasLetivosFecharMes" name="diasLetivosFecharMes[<?= $aluno['idAl'] ?>]" >
                    </td>
             
                    <td class="debug">
                        <input type="text" class="form-control inputAntiUe" name="AntiUe[<?= $aluno['idAl'] ?>]" >
                    </td>
                    <td class="debug">
                        <input type="text" id="saldoFinal-<?= $aluno['idAl'] ?>" class="form-control saldoFinalMes"  name="saldoFinalMes[<?= $aluno['idAl'] ?>]"  >
                    </td>
					<!--
                    <td class="center-text"><input type="text" class="form-control justificativa" value='<?= $aluno['justificativa'] ? $aluno['justificativa'] : '0'; ?>'  name="justificativa[<?= $aluno['idAl'] ?>]" ></td>
					-->
                    <td class="center-text">
                        <div class="<?= $aluno['valor'] ? '' : 'habilitadoNecessidadeCredito'; ?>">
                        <input id="valorNec-<?= $aluno['idAl'] ?>"  type="text" readonly="true" class="form-control valorNecessario" name="valorNecessario[<?= $aluno['idAl'] ?>]"  value="<?= $aluno['valor'] ? $aluno['valor'] : '0'; ?>">
                        </div>
                    </td>
                    <td class="center-td" style="max-width: 100px !important;">
                        <div class="<?= $aluno['valor'] ? '' : 'habilitadoNecessidadeCredito'; ?>">
                            <div class="simple-toggle">
                                <label class="tgl">
                                    <input type="checkbox" class="a checkboxNecessidadeCredito"   />
                                    <span data-on="Sim" data-off="Não"></span>
                                </label>
                            </div>
                        </div>
                        <div class="avisoNecessidadeCredito"  style="max-width: 100px !important;color:#D8544F">
                            <!-- <i class="fas fa-exclamation-triangle"></i> Necessário desmarcar -->
                        </div>
                    </td>

                    <td class="center-td"><a class="btn btn-primary pull-right consultarCredito" name="<?= $aluno['id'] ?>"><i class="fas fa-search"></i></a></td>
                </tr>
                <?php  
				$contador = $contador +1;
				endforeach; ?>
            </table>
            <div class="pull-right">
                <form class="form-inline">
                    <b>Alunos atendidos:</b> <input type="text" class="form-control qtdeAlunos"
                        style="max-width: 45px;padding-right:15px;" readonly="true" value="<?php echo$i?>" />
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
    <div class="" style="margin-top:20px;">
    </div>
       <div class="col-md-10">  </div>
	   <div class="col-md-1"> 
		<p  class="btn btn-primary pull-right"  name='salvarProgresso' id="salvarProgresso">Salvar Progresso</p>
		</div>
		<div class="col-md-1"> 
		<p  class="btn btn-danger pull-right"  name='salvar2' id="salvar2">Salvar e Finalizar</p>
		<!--
		<button  class="btn btn-danger pull-right" id="salvar" name="salvar">Salvar e Finalizar</button>
		-->
		</div>
		
		
    </div>                                                                                              
</div>
<?php ActiveForm::end(); ?>
<?= $this->render('calculos-passe', ['model' => $model, 'temSolCred' => $temSolCred]) ?>



<script type="text/javascript">
$(document).on('click', '#salvarProgresso', function () {
	
	let cont = 0;
	  $(".alunoMarcado").each(function() {
        if ($(this).prop('checked')) {
            cont++;
            inputAluno($(this), false)
        } else {
            inputAluno($(this), true)
        }
    });
	
	if(cont == 0){
		
		return Swal.fire({
			icon: 'warning',
			title: 'Atenção!',
			html: "É necessário selecionar algum aluno antes de salvar",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'OK',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (result.value) {
			}
		});
		
	}else{
		$("#statusProgresso").val('1');
		$("#formCredito").submit();
	}
	
	
  
});


</script>	