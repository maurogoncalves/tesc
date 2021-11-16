<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Usuario */

$this->title = 'Novo usuário';
$this->params['breadcrumbs'][] = ['label' => 'Usuários', 'url' => ['index']];
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
				'desabilitarPerfil' => $desabilitarPerfil,
		    ]) ?>

		</div>
	</div>
</div>
