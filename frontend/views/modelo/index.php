<?php
use yii\helpers\Html;
use kartik\grid\GridView;

use yii\widgets\Pjax;
use common\models\Escola;
use common\models\Marca;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Usuario;
use common\models\Condutor;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UsuarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Modelos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <?= Html::a('Novo modelo', ['create'], ['class' => 'btn btn-success pull-right']) ?>
            </div>
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'idMarca',
                            'value' => 'marca.nome',
                            'filter' => ArrayHelper::map(Marca::find()->all(), 'id', 'nome')
                        ],
                        'nome',
                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
