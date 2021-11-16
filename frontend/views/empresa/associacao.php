<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use \kartik\form\ActiveField;
use kartik\number\NumberControl;
use kartik\grid\GridView;

use kartik\widgets\FileInput;
use yii\data\ArrayDataProvider;
/* @var $this yii\web\View */
/* @var $model common\models\Empresa */

$this->title = $model->razaoSocial;
$this->params['breadcrumbs'][] = ['label' => 'Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">

                <div class="box-body">

                 <div class="nav-tabs-custom tab-primary">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#condutores" data-toggle="tab">Condutores</a></li>
                        <li class=""><a href="#veiculos" data-toggle="tab">Veículos</a></li>

                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="condutores">
                            <?= GridView::widget([
                                'dataProvider' => new ArrayDataProvider([
                                    'allModels' => $model->condutores,
                                    'key' => 'id',
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                ]),
                                'pjax' => true,
                                'pjaxSettings' =>[
                                    'neverTimeout'=>true,
                                    'options'=>[
                                            'id'=>'gridCondutores',
                                        ]
                                    ],
                                'striped' => false,
                                'bootstrap' => true,
                                'emptyText' => '<h3 class="vazio">Nenhum Condutor</h3>',
                                'options' => [
                                    'class' => 'table-header-ajax',
                                 ],
                                'columns' => [
                                    [
                                        'attribute' => 'nome',

                                        'label' => 'Nome',
                                        'filter' => true
                                    ],
                                    [
                                        'attribute' => 'cpf',
                                        'label' => 'CPF',
                                        'filter' => false
                                    ],
                                    [
                                        'attribute' => 'endereco',
                                        'label' => 'Endereço',
                                        'filter' => false
                                    ],
                                    [
                                        'attribute' => 'telefone',
                                        'filter' => false
                                    ],
                                    [
                                           'class' => 'yii\grid\ActionColumn',
                                           'template' => '{view}',
                                           'buttons' => [
                                                'view' => function ($url, $model) {
                                                    return Html::a('<i class="fa fa-fw fa-eye" aria-hidden="true"></i>', Url::to(['condutor/view','id' => $model->id]), [
                                                            'data-pjax' => 0,
                                                            // 'target' => '_blank',
                                                            'class' => 'btn btn-primary bth-xs',
                                                    ]);
                                                },
                                                // 'view' => function ($url, $model) {
                                                //     return Html::button('<i class="fa fa-fw fa-eye"></i>', ['value' => Url::to(['condutor/view-ajax', 'id' => $model->id]), 'title' => 'Condutor', 'class' => 'showModalButton btn btn-primary bth-xs']) ;
                                                // },
                                            ]
                                    ],
                                ],
                            ]); ?>
                            <div class="box-footer">

                                <?= Html::button('Novo Condutor', ['value' => Url::to(['condutor/create-ajax','idEmpresa' => $model->id]), 'title' => 'Condutor', 'class' => 'showModalButton btn btn-success pull-right']); ?>
                            </div>
                        </div>
                    <!-- /.tab-content -->
                        <div class="tab-pane" id="veiculos">
                            <?= GridView::widget([
                                'dataProvider' => new ArrayDataProvider([
                                    'allModels' => $model->veiculos,
                                    'key' => 'id',
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                ]),
                                'pjax' => true,
                                'pjaxSettings' =>[
                                    'neverTimeout'=>true,
                                    'options'=>[
                                            'id'=>'gridVeiculos',
                                        ]
                                    ],
                                'options' => [
                                    'class' => 'table-header-ajax',
                                ],
                                'striped' => false,
                                'bootstrap' => true,
                                'emptyText' => '<h3 class="vazio">Nenhum Veículo</h3>',
                                'columns' => [
                                    [
                                        'attribute' => 'placa',
                                        'filter' => false
                                    ],
                                    [
                                        'attribute' => 'capacidade',
                                        'label' => 'Capacidade',
                                        'filter' => false
                                    ],      
                                    [
                                           'class' => 'yii\grid\ActionColumn',
                                           'template' => '{view}',
                                           'buttons' => [
                                                 'view' => function ($url, $model) {

                                                    return Html::button('<i class="fa fa-fw fa-eye"></i>', ['value' => Url::to(['veiculo/view-ajax', 'id' => $model->id]), 'title' => 'Veículo', 'class' => 'showModalButton btn btn-primary bth-xs']) ;

                                                },
                                         
                                            ]
                                    ],

                                
                                ],
                            ]); ?>
                            <div class="box-footer">
                                <?= Html::button('Novo Veículo', ['value' => Url::to(['veiculo/create-ajax','idProprietarioEmpresa' => $model->id, 'tipoProprietario' => 2]), 'title' => 'Veículo', 'class' => 'showModalButton btn btn-success pull-right']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
