<?php

use common\models\Empresa;
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $model common\models\Empresa */

$this->title = $model->razaoSocial;
$this->params['breadcrumbs'][] = ['label' => 'Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
             <div class="box-header with-border">
                <p>
                     <?= Empresa::permissaoEditar() ? Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) : '' ?>
                    <?= Empresa::permissaoRemover() ?  Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ']) : '' ?>
                </p>
                </div>
                <div class="box-body">

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        // 'id',
                        'cnpj',
                        'nomeFantasia',
                        'razaoSocial',
                        // 'endereco',
                        [
                            'attribute' => 'cep',
                            'value' => function($model) {
                                return $model->cep;
                            }
                        ],
                        [
                            'attribute' => 'endereco',
                            'value' => function($model) {
                                $endereco  = $model->tipoLogradouro ? $model->tipoLogradouro.' '.$model->endereco : $model->endereco;
                                if($model->numeroResidencia)
                                    $endereco .= ' Nº '.$model->numeroResidencia;
                                return $endereco;
                            }
                        ],
                        [
                            'attribute' => 'bairro',
                            'value' => function($model) {
                                return $model->bairro;
                            }
                        ],
                        [
                            'attribute' => 'complementoResidencia',
                            'value' => function($model) {
                                return $model->complementoResidencia;
                            }
                        ],
                        // 'lat',
                        // 'lng',
                        'telefone',
                        'email:email',
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