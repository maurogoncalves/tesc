<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CondutorVinculo */

$this->title = 'Create Condutor Vinculo';
$this->params['breadcrumbs'][] = ['label' => 'Condutor Vinculos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-solid">
		    <div class="box-header with-border">
		    	<h3><?= Html::encode($this->title) ?></h3>
		    </div>

		    <?= $this->render('_formAjax', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
