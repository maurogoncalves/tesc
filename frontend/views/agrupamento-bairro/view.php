<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AgrupamentoBairro */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Agrupamento Bairros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">

                <h3><?= Html::encode($this->title) ?></h3>
            </div>
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
            'idBairro',
            'nome',
            'agrupamento',
                    ],
                ]) ?>
        </div>
    </div>
</div>
