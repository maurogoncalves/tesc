<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ReciboPagamentoAutonomo */

$this->title = 'Novo RPA';
$this->params['breadcrumbs'][] = ['label' => 'RPA', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
