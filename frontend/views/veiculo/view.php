<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Veiculo;
use common\models\Condutor;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\TipoDocumento;


// use limion\bootstraplightbox\BootstrapMediaLightboxAsset;
// BootstrapMediaLightboxAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Condutor */

$this->title = 'Veículo: '.$model->placa;
$this->params['breadcrumbs'][] = ['label' => 'Veículos', 'url' => ['index']];
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

                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="box-header with-border">
                <p>
                     <?= Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                    
                    <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ml-10']) ?>
                </p>
            </div>
             <div class="box-body">
                  <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        // 'idModelo',
                        'placa',
                        'capacidade',
                   
                        [
                        'attribute' => 'dataVistoriaMunicipal',
                        'value'=>  function($model){
                                return ($model->dataVistoriaMunicipal && $model->dataVistoriaMunicipal != '0000-00-00')? date("d/m/Y", strtotime($model->dataVistoriaMunicipal)):'-';

                            },
                        ],
                        [
                            'attribute' => 'dataVistoriaEstadual',
                            'value'=>  function($model){
                                   return ($model->dataVistoriaEstadual && $model->dataVistoriaEstadual != '0000-00-00')? date("d/m/Y", strtotime($model->dataVistoriaEstadual)): '-';
   
                               },
                           ],
                           [
                            'attribute' => 'dataVencimentoSeguro',
                            'value'=>  function($model){
                                   return ($model->dataVencimentoSeguro && $model->dataVencimentoSeguro != '0000-00-00')? date("d/m/Y", strtotime($model->dataVencimentoSeguro)) :'-';
   
                               },
                           ],
                        'anoFabricacao',
                        'anoModelo',
                        [
                            'attribute' => 'tipoVeiculo',
                            'value' => function($model){
                                return Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo];
                            },
                        ],
                        [
                            'attribute' => 'alocacao',
                            'value' => function($model){
                                return Veiculo::ARRAY_ALOCACAO[$model->alocacao];
                            },
                        ],                  
                    ],
                ]) ?>
            </div>
        </div>

    </div>
       <div class="col-md-6">
 
        <div class="row">
            <div class="col-md-12">
                          <div class="box box-solid">
     <div class="box-header with-border">

                <h3>Documentos</h3>
            </div>
    <div class="box-body" style="margin-left:10px;">

            <div class="row margin-bottom">
            <div class="box-header with-border"><h4><i class="fa fa-file" aria-hidden="true"></i> CRLV
            <?=  $model->docVistoriaMunicipal ? Html::a('Apagar arquivos', ['veiculo/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CRLV], [
                            'class' => 'btn btn-danger btn-sm pull-right align-button',
                            'data' => [
                            'confirm' => 'Tem certeza que deseja apagar arquivos?',
                            'method' => 'post',
                            ],
                            ]) : ''; ?>
            </h4></div>
            <?php if ($model->docCRLV) { 
                foreach ($model->docCRLV as $documento)
                {
                    $tipo = substr($documento->arquivo, -3);
                    $url = Url::to(['veiculo/delete-doc', 'id' => $documento->id]);
                    echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
                    // if (substr($documento->arquivo, -3) != 'pdf')
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="lightbox col-md-2"><img class="img-responsive" src="img/default.png"></a>';
                    // else
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="col-md-2"><img class="img-responsive" src="img/pdf.png" ></a>';
                }
            } else {
                echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Sem anexo.</div></div>';
            }  ?>
            </div>

            <div class="row margin-bottom">
            <div class="box-header with-border"><h4><i class="fa fa-file" aria-hidden="true"></i> Vistoria Estadual
            <?=  $model->docVistoriaMunicipal ? Html::a('Apagar arquivos', ['veiculo/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_VISTORIA_ESTADUAL], [
                            'class' => 'btn btn-danger btn-sm pull-right align-button',
                            'data' => [
                            'confirm' => 'Tem certeza que deseja apagar arquivos?',
                            'method' => 'post',
                            ],
                            ]) : ''; ?>
            </h4></div>
            <?php if ($model->docVistoriaEstadual) { 
                foreach ($model->docVistoriaEstadual as $documento)
                {
                    $tipo = substr($documento->arquivo, -3);
                    $url = Url::to(['veiculo/delete-doc', 'id' => $documento->id]);
                    echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
                    // if (substr($documento->arquivo, -3) != 'pdf')
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="lightbox col-md-2"><img class="img-responsive" src="img/default.png"></a>';
                    // else
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="col-md-2"><img class="img-responsive" src="img/pdf.png" ></a>';
                }
            } else {
                echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Sem anexo.</div></div>';
            }  ?>
            </div>


            <div class="row margin-bottom">
            <div class="box-header with-border"><h4><i class="fa fa-file" aria-hidden="true"></i> Vistoria Municipal
            <?=  $model->docVistoriaMunicipal ? Html::a('Apagar arquivos', ['veiculo/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_VISTORIA_MUNICIPAL], [
                            'class' => 'btn btn-danger btn-sm pull-right align-button',
                            'data' => [
                            'confirm' => 'Tem certeza que deseja apagar arquivos?',
                            'method' => 'post',
                            ],
                            ]) : ''; ?>
            </h4></div>
            <?php if ($model->docVistoriaMunicipal) { 
                foreach ($model->docVistoriaMunicipal as $documento)
                {
                    $tipo = substr($documento->arquivo, -3);
                    $url = Url::to(['veiculo/delete-doc', 'id' => $documento->id]);
                    echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
                    // if (substr($documento->arquivo, -3) != 'pdf')
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="lightbox col-md-2"><img class="img-responsive" src="img/default.png"></a>';
                    // else
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="col-md-2"><img class="img-responsive" src="img/pdf.png" ></a>';
                }
            } else {
                echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Sem anexo.</div></div>';
            }  ?>
            </div>

            <div class="row margin-bottom">
            <div class="box-header with-border"><h4><i class="fa fa-file" aria-hidden="true"></i> Apólice do Seguro
            <?=  $model->docApolice ? Html::a('Apagar arquivos', ['veiculo/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_APOLICE], [
                            'class' => 'btn btn-danger btn-sm pull-right align-button',
                            'data' => [
                            'confirm' => 'Tem certeza que deseja apagar arquivos?',
                            'method' => 'post',
                            ],
                            ]) : ''; ?>
            </h4></div>
          
            <?php if ($model->docApolice) { 
                foreach ($model->docApolice as $documento)
                {
                    $tipo = substr($documento->arquivo, -3);
                    $url = Url::to(['veiculo/delete-doc', 'id' => $documento->id]);
                    echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
                    // if (substr($documento->arquivo, -3) != 'pdf')
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="lightbox col-md-2"><img class="img-responsive" src="img/default.png"></a>';
                    // else
                    //     echo '<a href="'.$documento->arquivo.'" target="_new" class="col-md-2"><img class="img-responsive" src="img/pdf.png" ></a>';
                }
            } else {
                echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Sem anexo.</div></div>';
            }  ?>
            </div>

        </div>
    </div>
            </div>
        </div>
    </div>
</div>
<!-- 


getDocApolice -->
<?php 
    //Modal de exclusão
    echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
    ]);
?>
