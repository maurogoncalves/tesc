<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Calendario */

$this->title = 'Novo calendário';
$this->params['breadcrumbs'][] = ['label' => 'Calendários', 'url' => ['index']];
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
				//'events' => $events,
		    ]) ?>

		</div>
	</div>
</div>
