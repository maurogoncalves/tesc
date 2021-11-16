<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\Escola;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Usuario;
use yii\helpers\Url;
use common\models\Calendario;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UsuarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Calendários';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
     

          <div class="box-header with-border">
            <?= Calendario::permissaoCriar() ?  Html::a('Novo Calendário', ['create'], ['class' => 'btn btn-success pull-right']) : '' ?>

        </div>

        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    //'id',
                    [ 
                        'label' => 'Tipo de Escola',
                        'attribute' => 'tipoEscola',
                        'filter' =>  Escola::ARRAY_ENSINO,
                        'value' => function ($data) {
                            $escolas = [];
                            foreach ($data->calendarioEscolas as $escola)
                                $escolas[] = Escola::ARRAY_TIPO[$escola->tipoEscola];
                            return implode (', ', $escolas);
                        },
                    ],

                    [
                        'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna                
                        'class' => 'yii\grid\ActionColumn',
                        'template' => Calendario::permissaoActions(),
                        
                    ]
                ],
            ]); ?>
        </div>
    </div>
</div>
</div>
