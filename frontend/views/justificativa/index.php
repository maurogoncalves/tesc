<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\JustificativaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
use common\models\Justificativa;
$this->title = 'Justificativas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
       
          <div class="box-header with-border">
            <?= Html::a('Nova Justificativa', ['create'], ['class' => 'btn btn-success pull-right']) ?>
        </div>
        <div class="box-body">
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'nome',
             [
                    'attribute' => 'classificacao',
                    'value' => function($data) {
                        return $data->classificacao ? Justificativa::ARRAY_CLASSIFICACAO[$data->classificacao] : '-';
                    },
                    'filter' => Justificativa::ARRAY_CLASSIFICACAO
                ],

              [
                   'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna 
                   'class' => 'yii\grid\ActionColumn',
                   'template' => '{update} {delete}',
                   'buttons' => []
                ]
        ],
    ]); ?>
<?php Pjax::end(); ?>
        </div>
    </div>
</div>
</div>