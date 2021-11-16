<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NecessidadesEspeciaisSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Necessidades especiais';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
        
 
          <div class="box-header with-border">
            <?= Html::a('Nova necessidade especial', ['create'], ['class' => 'btn btn-success pull-right']) ?>
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
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete} ',
                    'buttons' => [
                    'delete' => function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,
                            [                                    
                            'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                            'pjax' => 0,                                            
                            'ok' => Yii::t('yii', 'Confirm'),
                            'cancel' => Yii::t('yii', 'Cancel'),
                            ],
                            ]);
                    }
                    ]
                    ]
                ],
            ]); ?>
            <?php Pjax::end(); ?>        
        </div>
    </div>
</div>
</div>