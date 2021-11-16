<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

use common\models\Escola;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper; 
/* @var $this yii\web\View */   
/* @var $searchModel common\models\EscolaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Roteirização';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row"> 
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
              <h3><?= Html::encode($this->title) ?></h3>
          </div>
 
        
        <div class="box-body">

            <?php Pjax::begin(); ?>  
            <!-- <marquee> 🚀🚀🚀Entrar em contato com a equipe de desenvolvimento.</marquee> -->
           <!--    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                 [
                    'attribute' => 'tipo',
                    'value' => function($model){
                        return $model->tipo ? Escola::ARRAY_TIPO[$model->tipo] : '-';

                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' =>  Escola::ARRAY_TIPO, 
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => '-',
                        
                    ]
                ],
                [
                    'attribute' => 'nome',
                    'value' => function($model){
                        return $model->nome;//Yii::t('app', $model->escola->nome);
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => ArrayHelper::map(Escola::find()->all(), 'nome', 'nome'), 
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => '-',
                        
                    ]
                ],
                [
                	'attribute' => 'alunos',
                    'value' => function($model){
                        return count($model->alunos);//Yii::t('app', $model->escola->nome);
                    },
                ],

                    //'endereco',
                    // 'lat',
                    // 'lng',
                    //'telefone',
                    // 'email:email',
                    // 'codigoCie',
                    [
                            
                           'class' => 'yii\grid\ActionColumn',
                           'template' => '{roteirizacao}',
                           'buttons' => [
                             'roteirizacao' => function ($url, $model) {
                                return  Html::a('<i class="fa fa-location-arrow" aria-hidden="true"></i>', $url, [
                                        'title' => Yii::t('app', 'Roteirização'),
                                ]);
                            },
                          
                        ]
                      ]
                    //['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?> -->
            <?php Pjax::end(); ?>        
        </div>
    </div>
</div>
</div>