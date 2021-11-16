<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AgrupamentoBairro */

$this->title = 'Atualizar agrupamento';
$this->params['breadcrumbs'][] = ['label' => 'Agrupamento Bairro', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Atualizar';
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
		    <div class="box-header with-border">
    			<h3><?= Html::encode($model->nome) ?></h3>
    		</div> 
		    <?= $this->render('_formEdit', [
				'model' => $model,
				'bairrosDisponiveis' => $bairrosDisponiveis,
				'bairrosPorZona' => $bairrosPorZona,
		    ]) ?>
    	</div>
    </div>
</div>
