<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CondutorVinculo */

$this->title = 'Update Condutor Vinculo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Condutor Vinculos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="row">
	<div class="col-md-6">
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
