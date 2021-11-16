<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use common\models\CondutorRota;


/* @var $this yii\web\View */
/* @var $model common\models\CondutorRota */

$this->title = 'Rota: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Condutor Rotas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6">
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
                        [
                            'attribute' => 'idCondutor',
                            'label' => 'Condutor',
                            'value' => function($model) {
                                return $model->condutor->nome;
                            },
                        ],
                        'viagem',
                        'descricao',
                        [
                            'attribute' => 'turno',                                   
                            'value' => function($model) {
                                return $model->turno ?  CondutorRota::ARRAY_TURNOS[$model->turno] : '-';
                            },
                        ],
                        [
                            'attribute' => 'sentido',
                            'label' => 'Sentido',
                            'value'=>  function($model){
                                return $model->sentido ? CondutorRota::ARRAY_SENTIDO[$model->sentido] : '-';
                            },
                        ],  
                    ],
                ]) ?>
            </div>
        </div>
    </div>


    <div class="col-md-6">
         <div class="box box-solid">
                <div class="box-header with-border">
                    <h3>Alunos na rota</h3>
                </div>
                 <div class="box-header with-border">

                    <?= Html::button('Atribuir aluno', ['value' => Url::to(['aluno-rota/create-ajax','idCondutorRota' => $model->id, 'idCondutor' => $model->idCondutor ]), 'title' => 'Atribuir aluno', 'class' => 'showModalButton btn btn-success pull-right']); ?>
                </div>
                <div class="box-body" style="margin-left:10px;">
                <?= GridView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->alunoRota,
                        'key' => 'id',
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                    ]),
                    'pjax' => true,
                    'pjaxSettings' =>[
                        'neverTimeout'=>true,
                        'options'=>[
                                'id'=>'grid',
                            ]
                        ],
                    'options' => [
                        'class' => 'table-header-ajax',
                     ],
                    'striped' => false,
                    'bootstrap' => true,
                    'emptyText' => '<h3 class="vazio">Nenhum aluno</h3>',
                    'columns' => [

                        [
                            'attribute' => 'idAluno',
                            'label' => 'Aluno',
                            'value'=>  function($model){
                                    return $model->aluno->nome;
                            },
                        ],
                        [
                            'attribute' => 'idEscola',
                            'label' => 'Escola',
                            'value'=>  function($model){
                                    return $model->escola->nome;
                            },
                        ],
                        // [
                        //     'attribute' => 'turno',
                        //     'label' => 'Turno',
                        //     'filter' => false,
                        //     'value' => function($model) {
                        //         return $model->turno ?  CondutorRota::ARRAY_TURNOS[$model->turno] : '-';
                        //     }
                        // ],
                        //   [
                        //     'attribute' => 'sentido',
                        //     'label' => 'Sentido',
                        //     'value'=>  function($model){
                        //             return $model->sentido ? CondutorRota::ARRAY_SENTIDO[$model->sentido] : '-';
                        //     },
                        // ],      
                        [
                               'class' => 'yii\grid\ActionColumn',
                               'template' => '{delete}  ',
                               'buttons' => [
                       
                                      'delete' => function($url, $model){
                                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['aluno-rota/delete', 'id' => $model->id],
                                                [                                    
                                                'data' => [
                                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                                'method' => 'post',
                                                'pjax' => 1,                                            
                                                'ok' => Yii::t('yii', 'Confirm'),
                                                'cancel' => Yii::t('yii', 'Cancel'),
                                                ],
                                                ]);
                                        },
                                   
                             
                                ]
                        ],

                    
                    ],
                ]); ?>
            </div>
        </div>
    </div>


</div>
