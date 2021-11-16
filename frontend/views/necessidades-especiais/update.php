<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NecessidadesEspeciais */

$this->title = 'Atualizar necessidade especial';
$this->params['breadcrumbs'][] = ['label' => 'Necessidades especiais', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->nome]];
$this->params['breadcrumbs'][] = 'Atualizar';
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
