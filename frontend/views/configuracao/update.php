<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Configuracao */

$this->title = 'Configurações';
$this->params['breadcrumbs'][] = ['label' => 'Configurações', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
// $this->params['breadcrumbs'][] = 'Editar';
?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-solid">
	
		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>
</div>
