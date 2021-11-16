<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ReciboPagamentoAutonomo */

$this->title = 'Atualizar RPA';
$this->params['breadcrumbs'][] = ['label' => 'RPA', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Atualizar';
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-solid">
		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>
</div>
