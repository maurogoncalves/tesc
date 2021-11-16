<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SolicitacaoTransporte */

$this->title = 'Atualizar solicitação de transporte';
$this->params['breadcrumbs'][] = ['label' => 'Solicitação de transporte', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Atualizar';
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
		    <div class="box-header with-border">
		    	<h3><?= Html::encode($this->title) ?></h3>
		    </div>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>