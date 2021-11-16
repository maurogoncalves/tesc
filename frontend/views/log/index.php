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
use common\models\SolicitacaoCredito;
use common\models\Escola;
use common\models\Condutor;
use common\models\Log;
use common\models\Usuario;
use common\models\Aluno;
use common\models\Marca;
use common\models\Modelo;
use common\models\Veiculo;
use common\models\CondutorRota;
use common\models\Justificativa;
use common\models\ReciboPagamentoAutonomo;
$this->title = 'Histórico de alterações';
$this->params['breadcrumbs'][] = ['label' => 'Histórico de alterações', 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
$arrayAnos = [];

?>
<script>
var tabelaAtual = '<?= $get['tabela'] ?>';
</script>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
		
			<div class="box-body">
			    <?= Html::beginForm(['log/index'], 'GET', ['id' => 'formFilter']); ?>
			    <div class="row form-group">
				    <div class="row">
						<div class="col-md-3">
							<?php
								echo Html::label('Período', 'periodo');
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
							<?php
								echo Html::label('Ação', 'acao');
									echo Select2::widget([
									'name' => 'acao',
									'attribute' => 'acao',
									'data' => Log::ARRAY_ACAO,
										'value' => isset($_GET['acao']) ? $_GET['acao'] : '',
									'options' => ['placeholder' => 'Selecione a ação'],
									'pluginOptions' => [
										'allowClear' => true
									],
									]);
							?>
						</div>

						<div class="col-md-3">
							<?php
								echo Html::label('Usuário', 'idUsuario');
									echo Select2::widget([
										'name' => 'idUsuario',
										'attribute' => 'idUsuario',
										'value' => isset($_GET['idUsuario']) ? $_GET['idUsuario'] : '',
										'data' => ArrayHelper::map(Usuario::find()->all(), 'id', 'nome'),
										'options' => ['placeholder' => 'Selecione o usuário'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
						</div>
						<div class="col-md-3">
							<?php
								echo Html::label('Filtrar por', 'tabela');
									echo Select2::widget([
										'name' => 'tabela',
										'attribute' => 'tabela',
										'value' => isset($_GET['tabela']) ? $_GET['tabela'] : '',
										'data' => Log::ARRAY_TABELA,
										'options' => ['placeholder' => 'Selecione a forma de filtro'],
										'pluginOptions' => [
											'allowClear' => true
										],
										'pluginEvents' => [
											"change" => 'function() { 
												$(".input").css("display", "none");
												$("#"+tabelaAtual+"-input").val(null).trigger("change");
												
												tabelaAtual = $(this).val();
												$("#"+tabelaAtual).css("display", "block");
												
											}',
										],
									]);
							?>
						</div>
					</div> 
        <!--
        'SolicitacaoCredito' => 'Solicitação de crédito',
        'SolicitacaoTransporte' => 'Solicitação de transporte', -->
        
					<div class="row">
						<div id="Aluno" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label('Aluno', 'idAluno');
									echo Select2::widget([
										'name' => 'idAlunoTable',
										'attribute' => 'idAlunoTable',
										'value' => isset($_GET['idAlunoTable']) ? $_GET['idAlunoTable'] : '',
										'data' => ArrayHelper::map(Aluno::find()->all(), 'id', 'nome'),
										'options' => ['id' => 'Aluno-input','placeholder' => 'Selecione o aluno'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
						<div id="Escola" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label('Escola', 'idEscolaTable');
									echo Select2::widget([
										'name' => 'idEscolaTable',
										'attribute' => 'idEscolaTable',
										'value' => isset($_GET['idEscolaTable']) ? $_GET['idEscolaTable'] : '',
										'data' => ArrayHelper::map(Escola::find()->all(), 'id', 'nome'),
										'options' => ['id' => 'Escola-input','placeholder' => 'Selecione o aluno'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
						<div id="Marca" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label("Marca", 'idMarcaTable');
									echo Select2::widget([
										'name' => 'idMarcaTable',
										'attribute' => 'idMarcaTable',
										'value' => isset($_GET['idMarcaTable']) ? $_GET['idMarcaTable'] : '',
										'data' => ArrayHelper::map(Marca::find()->all(), 'id', 'nome'),
										'options' => ['id' => 'Marca-input','placeholder' => 'Selecione'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
					
						<div id="Modelo" style="display:none;" class="input"> 
							<div class="col-md-3">
								<?php
									echo Html::label("Modelo", 'idModeloTable');
										echo Select2::widget([
											'name' => 'idModeloTable',
											'attribute' => 'idModeloTable',
											'value' => isset($_GET['idModeloTable']) ? $_GET['idModeloTable'] : '',
											'data' => ArrayHelper::map(Modelo::find()->all(), 'id', 'nome'),
											'options' => ['id' => 'Modelo-input','placeholder' => 'Selecione'],
											'pluginOptions' => [
												'allowClear' => true
											],
										]);
								?>
							</div>
						</div>
						<div id="Veiculo" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label("Veículo", 'idVeiculoTable');
									echo Select2::widget([
										'name' => 'idVeiculoTable',
										'attribute' => 'idVeiculoTable',
										'value' => isset($_GET['idVeiculoTable']) ? $_GET['idVeiculoTable'] : '',
										'data' => ArrayHelper::map(Veiculo::find()->all(), 'id', 'placa'),
										'options' => ['id' => 'Veiculo-input','placeholder' => 'Selecione'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
						<div id="Condutor" style="display:none;" class="input"> 
								<div class="col-md-3">
								<?php
									echo Html::label("Condutor", 'idCondutorTable');
										echo Select2::widget([
											'name' => 'idCondutorTable',
											'attribute' => 'idCondutorTable',
											'value' => isset($_GET['idCondutorTable']) ? $_GET['idCondutorTable'] : '',
											'data' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'),
											'options' => ['id' => 'Condutor-input','placeholder' => 'Selecione'],
											'pluginOptions' => [
												'allowClear' => true
											],
										]);
								?>
								</div>
							</div>
						</div>
				

				
						<div id="Usuario" style="display:none;" class="input"> 
							<div class="col-md-3">
								<?php
									echo Html::label("Usuario", 'idUsuarioTable');
										echo Select2::widget([
											'name' => 'idUsuarioTable',
											'attribute' => 'idUsuarioTable',
											'value' => isset($_GET['idUsuarioTable']) ? $_GET['idUsuarioTable'] : '',
											'data' => ArrayHelper::map(Usuario::find()->all(), 'id', 'nome'),
											'options' => ['id' => "Usuario-input",'placeholder' => 'Selecione'],
											'pluginOptions' => [
												'allowClear' => true
											],
										]);
								?>
							</div>
						</div>
						<div id="CondutorRota" style="display:none;"  class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label("Rota", 'idCondutorRotaTable');
									echo Select2::widget([
										'name' => 'idCondutorRotaTable',
										'attribute' => 'idCondutorRotaTable',
										'value' => isset($_GET['idCondutorRotaTable']) ? $_GET['idCondutorRotaTable'] : '', 
										'data' => ArrayHelper::map(CondutorRota::find()->all(), 'id', 'nomeRota'),
										'options' => ['id' => "CondutorRota-input",'placeholder' => 'Selecione'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
						<div id="Justificativa" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label("Justificativa", 'idJustificativaTable');
									echo Select2::widget([
										'name' => 'idJustificativaTable',
										'attribute' => 'idJustificativaTable',
										'value' => isset($_GET['idJustificativaTable']) ? $_GET['idJustificativaTable'] : '',
										'data' => ArrayHelper::map(Justificativa::find()->all(), 'id', 'nome'),
										'options' => ['id' => "Justificativa-input",'placeholder' => 'Selecione'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
				
						<div id="ReciboPagamentoAutonomo" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label("RPA", 'idReciboPagamentoAutonomoTable');
									echo Select2::widget([
										'name' => 'idReciboPagamentoAutonomoTable',
										'attribute' => 'idReciboPagamentoAutonomoTable',
										'value' => isset($_GET['idReciboPagamentoAutonomoTable']) ? $_GET['idReciboPagamentoAutonomoTable'] : '',
										'data' => ArrayHelper::map(ReciboPagamentoAutonomo::find()->all(), 'id', 'nome'),
										'options' => ['id' => "ReciboPagamentoAutonomo-input",'placeholder' => 'Selecione'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
						<div id="SolicitacaoTransporte" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label("SolicitacaoTransporte", 'idSolicitacaoTransporteTable');
									echo Select2::widget([
										'name' => 'idSolicitacaoTransporteTable',
										'attribute' => 'idSolicitacaoTransporteTable',
										'value' => isset($_GET['idSolicitacaoTransporteTable']) ? $_GET['idSolicitacaoTransporteTable'] : '',
										'data' => ArrayHelper::map(SolicitacaoTransporte::find()->all(), 'id', 'nome'),
										'options' => ['id' => "SolicitacaoTransporte-input",'placeholder' => 'Selecione'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
						</div>
						<div id="SolicitacaoCredito" style="display:none;" class="input"> 
							<div class="col-md-3">
							<?php
								echo Html::label("Solicitação de crédito", 'idSolicitacaoCreditoTable');
									echo Select2::widget([
										'name' => 'idSolicitacaoCreditoTable',
										'attribute' => 'idSolicitacaoCreditoTable',
										'value' => isset($_GET['idSolicitacaoCreditoTable']) ? $_GET['idSolicitacaoCreditoTable'] : '', 
										'data' => ArrayHelper::map(SolicitacaoCredito::find()->all(), 'id', 'nome'),
										'options' => ['id' => "SolicitacaoCredito-input",'placeholder' => 'Selecione'],
										'pluginOptions' => [
											'allowClear' => true
										],
									]);
							?>
							</div>
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
<div class="row">
	<section class="col-md-12">
		<div class="box box-solid">
		  <div class="box-body">
			  <?php 
				$columns = [
					[
						'class' => '\kartik\grid\DataColumn',
						'attribute' => 'data',
						// 'filterType' => GridView::FILTER_DATE,
						// 'value' => function($model) {
						//     $d = new DateTime($model->data);
						//     return $d->format('d/m/Y H:i');
						// },
						'format' => ['date', 'php:d/m/Y H:i'],

						// 'filterType' => GridView::FILTER_DATE_RANGE,
						
					],
					[
						'value' => 'usuario.nome',
						'label' => 'Usuário'
					],
					[
						'attribute' => 'acao',
						'value' => function($model) {
							return Log::ARRAY_ACAO[$model->acao];
						}
					],
					'tabela',
				
				];
				
				switch($get['tabela']){
					// case 'Veiculo': $columns[] = 'veiculoTable.placa';  break;
					default: break;
				}
				$columns[] = 'referencia';
				$columns[] = 'coluna';
				$columns[] = 'antes';
				$columns[] = 'depois';
					
				// $columns[] = 'idModeloTable';
				// $columns[] = 'idMarcaTable';
				// $columns[] = 'idEscolaTable';
				
			  ?>
		      <?= GridView::widget([
				'panel' => [
					'heading'=>false,
					'type'=>false,
					'showFooter'=>false
				],
				'toolbar' =>  [
					'{export}{toggleData}',
				],
		        'dataProvider' => new ArrayDataProvider([
		          'allModels' => $arrayData,
		          'sort' => [
		              'attributes' => [
						// 'data',
						// ''
		                // 'status',
		                // 'inicio',
		                // 'fim',
		                // 'criado',
		                // 'creditoAdministrativo',
		                // 'total'
		              ],
		          ],
		          'pagination' => [
		              'pageSize' => 10,
		          ],
		        ]),
		        'columns' => $columns
		      ]); ?>
		  </div>
		</div>
	</section>
</div>

<script type="text/javascript">
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
	$("#"+tabelaAtual).css("display", "block");
</script>
