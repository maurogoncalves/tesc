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

$this->title = 'Valor pago aos Condutores';
$this->params['breadcrumbs'][] = ['label' => 'Painel de indicadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$arrayAnos = [];

?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
			<div class="box-body">
			    <?= Html::beginForm(['painel-indicadores/valor-condutores'], 'GET', ['id' => 'formFilter']); ?>
			    <div class="row form-group">
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
					    	echo Html::label('Condutor', 'condutor');
								echo Select2::widget([
									'name' => 'condutor',
							    'attribute' => 'idCondutor',
							    'data' => $condutores,
									'value' => $_GET['condutor'] ? $_GET['condutor'] : '',
							    'options' => ['placeholder' => 'Selecione o condutor'],
							    'pluginOptions' => [
							        'allowClear' => true
							    ],
								]);
					    ?>
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
		  <div class="box-header with-border">
		    <h4><?= $this->title ?></h4>
		  </div>

		  <div class="box-body">
		    <?php Pjax::begin(); ?>
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
		          'allModels' => $rpa,
		          'sort' => [
		              'attributes' => [
		                'idCondutor',
		                'numRecibo',
		                'data',
		                'dias',
		                'valor',
		              ],
		          ],
		          'pagination' => [
		              'pageSize' => 10,
		          ],
		        ]),
		        'columns' => [
		          [
		            'attribute' => 'idCondutor',
                'label' => 'Condutor',
		            'value' => 'condutor.nome'
		          ],
		          'numRecibo',
		          [
		            'attribute' => 'data',
		            'value' => function ($model) {
		              $data = explode('-', $model->data);
		              return $data[2] . '/' . $data[1] . '/' . $data[0];
		            }
		          ],
		          'dias',
		          'valor',
		        ],
		      ]); ?>
		    <?php Pjax::end(); ?>
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

</script>
