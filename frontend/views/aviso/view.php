<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Veiculo;
use common\models\Aviso;
use common\models\CondutorRota;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use yii\helpers\Url;

use kartik\widgets\FileInput;
use yii\data\ArrayDataProvider;
use common\models\Escola;
use common\models\Usuario;
use common\models\TipoDocumento;
// use limion\bootstraplightbox\BootstrapMediaLightboxAsset;
// BootstrapMediaLightboxAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Condutor */

$this->title = 'Aviso: #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Avisos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
// function getTitleRight($model){
//   //return 'Veículo';
//     $name = 'Veículo: ';
//     if($model->veiculo->placa)
//         return $name.$model->veiculo->placa;
//     return $name.' (Não definido)';
// }
?>

<div class="row">
    <div class="col-md-6"> 
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3>Dados do aviso</h3>
                <p>
                     <?= Aviso::permissaoRemover() ? Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) : ''; ?>
                    
                    <?= Aviso::permissaoEditar() ? Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ml-10']) : ''; ?>
                </p>
            </div>
             <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                    'id',
                    'titulo',
                    [
                        'attribute' => 'mensagem', 
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'data',
                        'value' => function($model) {
                            return ($model->data) ? Yii::$app->formatter->asDate($model->data, 'dd/MM/Y') : '';
                        }
                    ],
                    [
                        'attribute' => 'idUsuario',
                        'label' => 'Usuário',
                        'value' =>  function($model) {
                            return $model->usuario->nome;
                        }
                    ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
