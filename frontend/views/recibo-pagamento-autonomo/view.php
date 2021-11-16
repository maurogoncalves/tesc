<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Aluno;
use kartik\dialog\Dialog;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $model common\models\Aluno */

$this->title = 'RPA: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'RPA', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <p>
                     <!-- <?= Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                    
                    <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ']) ?>
                    -->
                </p>
            </div>
             <div class="box-body">

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'numRecibo',
                       [
                         'label' => 'Condutor',
                         'attribute'=>  function($model){
                                return $model->condutor->nome;

                            },
                        ],
                        [
                         'label' => 'Data',
                         'attribute'=>  function($model){
                                return ($model->data && $model->data != '0000-00-00')?Yii::$app->formatter->asDate($model->data, 'dd/MM/Y'):'-';

                            },
                        ],
                    ],
                ]) ?>
            </div>
       </div>
    </div>
</div>
<?php 
    //Modal de exclusão
    echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
    ]);
?>