<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AgrupamentoBairro */

$this->title = 'Novo agrupamento de bairro';
$this->params['breadcrumbs'][] = ['label' => 'Agrupamento de bairros', 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
		    <div class="box-header with-border">
		    	<h3><?= Html::encode($this->title) ?></h3>
		    </div>
 
		    <?= $this->render('_form', [
				'model' => $model,
				'bairrosDisponiveis' => $bairrosDisponiveis 
		    ]) ?>

		</div>
	</div>
</div>

