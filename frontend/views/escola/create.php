<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Escola */

$this->title = 'Nova escola';
$this->params['breadcrumbs'][] = ['label' => 'Escolas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
