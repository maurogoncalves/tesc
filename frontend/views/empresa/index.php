<?php

use common\models\Empresa;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EmpresaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Empresas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
      

          <div class="box-header with-border">

            <?= Empresa::permissaoCriar() ? Html::a('Nova Empresa', ['create'], ['class' => 'btn btn-success pull-right']) : '' ?>
        </div>

        <div class="box-body">
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'cnpj',
            'razaoSocial',
            'nomeFantasia',
            'endereco',
            // 'lat',
            // 'lng',
            // 'telefone',
            // 'email:email',

                [
                'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna
               'class' => 'yii\grid\ActionColumn',
               'template' => Empresa::permissaoActions(), 
               'buttons' => [
                 'associacao' => function ($url, $model) {
                    return  Html::a('<span class=" glyphicon glyphicon-cog"></span>', $url, [
                            'title' => Yii::t('app', 'Associar Condutores e Veículos'),
                    ]);
                },
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