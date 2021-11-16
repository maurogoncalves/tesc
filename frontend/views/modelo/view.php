<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Usuario;
use kartik\grid\GridView;
use kartik\dialog\Dialog;
use kartik\select2\Select2;
use common\models\Marca;

use yii\helpers\ArrayHelper; 
/* @var $this yii\web\View */
/* @var $model common\models\Usuario */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Modelo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">

                <h3><?= Html::encode($this->title) ?></h3>
            </div>
             <div class="box-header with-border">
                <p>

                     <?= Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'id' => 'btn-dialog',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ']) ?>
                </p>
                </div>
                <div class="box-body">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
             
                        'nome',
                      [
                            'attribute' => 'idMarca',
                            'value' => function($model){
                                return $model->marca->nome;

                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' =>   ArrayHelper::map(Marca::find()->all(), 'id', 'nome'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                                
                            ]
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php 
    echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
    ]);
?>

