<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dialog\Dialog;
use common\models\Ocorrencia;

/* @var $this yii\web\View */
/* @var $model common\models\Ocorrencia */

$this->title = 'Ocorrência: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ocorrencias', 'url' => ['index']];
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
                <?= Ocorrencia::permissaoRemover() ? Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'data' => [
                        'confirm' => 'Tem certeza que deseja excluir este item?',
                        'method' => 'post',
                        ],
                        ]) : ''; ?>
                </p>
            </div>
             <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                         [
                            'attribute' => 'data',
                            'value' => function($model) {
                                   return ($model->data)?Yii::$app->formatter->asDateTime($model->data, 'dd/MM/Y HH:i:s'):'-';
                            }
                        ],

                        // 'idCondutor',
                        [
                            'attribute' => 'idCondutor',
                            'value' => function($model) {
                                return $model->condutor->nome;
                            }
                        ],
                        [
                            'attribute' => 'idCondutorRota',
                            'value' => function($model) {
                                return $model->condutorRota->nomeRota;
                            }
                        ],
                        [
                            'attribute' => 'idJustificativa',
                            'value' => function($model) {
                                return $model->justificativa->nome;
                            }
                        ],  
                        [
                            'attribute' => 'idVeiculo',
                            'value' => function($model) {
                                return $model->veiculo->placa;
                            }
                        ],  
                        
                        'descricao',
                    ],
                ]) ?>
             </div>
         </div>
    </div>
    <div class="col-md-6">
    <div class="box box-solid">
            <div class="box-header with-border">
                <h3>Fotos</h3>
            </div>
    <div class="box-body">
        <div class="gallery">
           <?php foreach($model->fotos as $foto){
               print '<a href="arquivos/'.$foto->arquivo.'"><img src="arquivos/'.$foto->arquivo.'" alt="" title="" width="120" height="120" style="margin-left:4px;"/></a>';
           } ?> 
        </div>
    </div>
        </div>
    </div>
</div>
<script>
    
$(function(){
		var $gallery = $('.gallery a').simpleLightbox();
		$gallery.on('show.simplelightbox', function(){
			console.log('Requested for showing');
        })
    });
</script>
<!-- 


getDocApolice -->
<?php 
    //Modal de exclusão
    echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
    ]);
?>