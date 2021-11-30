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
<?= $this->render('header-solicitacao', ['model' => $model, 'configuracao' => $configuracao]) ?>
<div class="row mt-10">
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
                    <th>Justificativa </th>
                    <th>Valor Necessário </th>
                    <th>Necessidade de crédito</th>
                    <th>&nbsp;</th>
                </tr>
                <?php $i = 0;
                foreach ($alunos as $aluno) :  $i++; ?>
                <tr>
                    <td class="center-td"><?= $i ?></td>
                    <td class="center-td"><input type="checkbox" class="alunoMarcado" name="aluno[]" value="<?= $aluno->id ?>" /></td>
                    <?php if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR): ?>
                    <td class="center-td"><input type="checkbox" class="fundhas"  name="fundhas[<?= $aluno->id ?>]" /></td>
                    <?php endif; ?>
                    <td class="center-text"><a target="_new"
                            href="<?= Url::toRoute(['aluno/view', 'id' =>  $aluno->id]) ?>"><?= $aluno->nome ?></a></td>
                    <td class="center-text"><span data-toggle="tooltip"
                            title="AULA(S) NA SEMANA:<?php foreach ($aluno->alunoCurso as $alunoCurso) : echo ' ' . Aluno::ARRAY_DIAS_CURSO[$alunoCurso->dia]; endforeach; ?>"><?= $aluno->RACompleto ?></span></td>
                    <td class="center-text"><?= $aluno->turma ? Aluno::ARRAY_SERIES[$aluno->serie].'/'.Aluno::ARRAY_TURMA[$aluno->turma] : '-' ?></td>

                    <td class="center-text"><?= $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? $aluno->solicitacao->cartaoPasseEscolar : $aluno->solicitacao->cartaoValeTransporte ?></td>

                    <td class="center-text">
                        <input type="text" class="form-control money inputSaldoRestante" name="saldoRestante[<?= $aluno->id ?>]" readonly="true">
                    </td>
                    
                    <td class="debug">
                        <input type="text" class="form-control inputDiasLetivosFecharMes" name="diasLetivosFecharMes[<?= $aluno->id ?>]" readonly="true">
                    </td>
             
                    <td class="debug">
                        <input type="text" class="form-control inputAntiUe" name="AntiUe[<?= $aluno->id ?>]" readonly="true">
                    </td>
                    <td class="debug">
                        <input type="text" class="form-control saldoFinalMes" name="saldoFinalMes[<?= $aluno->id ?>]" readonly="true">
                    </td>
                    <td class="center-text"><input type="text" class="form-control justificativa"  name="justificativa[<?= $aluno->id ?>]" readonly="true"></td>
                    <td class="center-text">
                        <div class="habilitadoNecessidadeCredito">
                        <input type="text" class="form-control valorNecessario" name="valorNecessario[<?= $aluno->id ?>]" readonly="true" value="0">
                        </div>
                    </td>
                    <td class="center-td" style="max-width: 100px !important;">
                        <div class="habilitadoNecessidadeCredito">
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

                    <td class="center-td"><a class="btn btn-primary pull-right consultarCredito"
                            aluno="<?= $aluno->id ?>"><i class="fas fa-search"></i></a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div class="pull-right">
                <form class="form-inline">
                    <b>Alunos atendidos:</b> <input type="text" class="form-control qtdeAlunos"
                        style="max-width: 45px;padding-right:15px;" readonly="true" value="0" />
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
    <div class="" style="margin-top:20px;">
    </div>
        <button  class="btn btn-primary pull-right" id="salvar">Salvar e Continuar</button>
    </div>                                                                                              
</div>
<?php ActiveForm::end(); ?>
<?= $this->render('calculos-passe', ['model' => $model]) ?>