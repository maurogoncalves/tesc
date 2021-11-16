<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Condutor */

$this->title = 'Create Condutor';
$this->params['breadcrumbs'][] = ['label' => 'Condutors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-primary">
		    <div class="box-header with-border">
		    	<h3><?= Html::encode($this->title) ?></h3>
		    </div>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
