<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\Escola;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Usuario;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UsuarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuários';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
    

          <div class="box-header with-border">
            <?= Html::a('Novo Usuário', ['create'], ['class' => 'btn btn-success pull-right']) ?>
        </div>

        <div class="box-body">
            <?php Pjax::begin(); ?>    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    // ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    [
                    'attribute' => 'idPerfil',
                        'value' => function($model){
                            return $model->idPerfil ? Usuario::ARRAY_PERFIS[$model->idPerfil] : '-';

                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' =>  Usuario::ARRAY_PERFIS, 
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => [
                            'placeholder' => '-',
                            
                        ]
                    ],
                    'nome',
                    'username',
                    'email:email',
            // 'authKey',
            // 'passwordHash',
            // 'passwordResetToken',
            // 'idFirebase',
            // 'status',
            // 'imagem',

                  [
                    'contentOptions' => ['style' => 'min-width:80px;'],  
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