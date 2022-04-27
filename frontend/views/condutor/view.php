<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Veiculo;
use common\models\Condutor;
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

$this->title = 'Condutor: '.$model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Condutores', 'url' => ['index']];
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
                <div class="row">
                    <div class="col-xs-6"><h3>Dados do condutor</h3></div>
                    <div class="col-xs-6 text-right pt-3">
                        <?= Condutor::permissaoEditar() ? Html::a('Editar Documentos', ['update', 'id' => $model->id], ['class' => 'btn btn-primary mr-10']) : ''; ?>
                        <?= Condutor::permissaoRemover() ? Html::a('Apagar', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger align-button',
                            'data' => [
                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                'method' => 'post',
                            ],
                        ]) : ''; ?>
                    </div>
                </div>
            </div>
            
            <?php if(!empty($model->fotoMotorista)) { ?>
                <?= Html::img('@web/'.$model->fotoMotorista, ['alt'=>'', 'class'=>'img-thumbnail m-3','style'=>'width:300px']);?>
            <?php } else { ?>
                <?= Html::img('@web/img/default.png', ['alt'=>'', 'class'=>'img-thumbnail m-3','style'=>'width:300px']);?>
            <?php } ?>
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [ 
						'pendencias',
                       'id',
                        [
                            'attribute' => 'Status',
                            'value'=>  function($model){
                                   return  Condutor::ARRAY_STATUS[$model->status];
                           },
                       ],
					   
                       'alvara',
                       [
                            'label' => 'Região de Atuação',
                            'value' => function ($model) {
                                if ($model->regioes) {
                                    return $model->getRegioesAsString();
                                } else {
                                    return '-';
                                }
                            }
                        ],
                        [
                         'label' => 'Data de Nascimento',
                         'attribute'=>  function($model){
                                return ($model->dataNascimento && $model->dataNascimento != '0000-00-00')? date("d/m/Y", strtotime($model->dataNascimento)) :'-';
                            },
                        ],
                        'inscricaoMunicipal', 
                        [
                            'label' => 'CPF',
                            'attribute' => 'CpfFormatado'
                        ],
                        [
                            'label' => 'RG',
                            'value' => function($model) {
                                return $model->rg.' '.$model->orgaoEmissor;
                            }
                        ],
                        [
                            'attribute' => 'nit',
                            'format' => ['NIT']
                        ],
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
                        // 'bairro',
                        [
                            'attribute' => 'telefone',
                            'format' => ['Telefone']
                        ],
                        [
                            'attribute' => 'telefone2',
                            'format' => ['Telefone']
                        ],
                        [
                            'attribute' => 'celular',
                            'format' => ['Telefone']
                        ],
                        [
                            'attribute' => 'celular2',
                            'format' => ['Telefone']
                        ],
                        'email:email',
                        'cnhRegistro',
                        [
                         'label' => 'Validade da CNH',
                         'attribute'=>  function($model){
                                return ($model->cnhValidade && $model->cnhValidade != '0000-00-00')? date("d/m/Y", strtotime($model->cnhValidade)) :'-';
                            },
                        ],
                        [
                         'label' => 'Início do contrato',
                         'attribute'=>  function($model){
                                return ($model->dataInicioContrato && $model->dataInicioContrato != '0000-00-00')? date("d/m/Y", strtotime($model->dataInicioContrato)) :'-';

                            },
                        ],
                        [
                         'label' => 'Fim do contrato',
							'attribute'=>  function($model){
                                return ($model->dataFimContrato && $model->dataFimContrato != '0000-00-00')? date("d/m/Y", strtotime($model->dataFimContrato)) :'-';
                            },
                        ],
                        'valorPagoKmViagem',
                        [
                            'attribute' => 'folhaPonto',
							'label' => 'Link 1',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<a target="_blank" href="'.$model->folhaPonto.'">'.$model->folhaPonto.'</a>';
                            }
                        ],
                        [
                            'attribute' => 'pesquisaRota',
							'label' => 'Link 2',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<a target="_blank" href="'.$model->pesquisaRota.'">'.$model->pesquisaRota.'</a>';
                            }
                        ],
                        
                     
                    ]
                ]); ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3>Escolas</h3>
            </div>
            <div class="box-header with-border">
			<?php if($model->status <> 2){?>
                 <?= CondutorRota::permissaoEditar() ? Html::button('Atribuir escola', ['value' => Url::to(['condutor-escola/create-ajax','idCondutor' => $model->id ]), 'title' => 'Atribuir escola', 'class' => 'showModalButton btn btn-success pull-right']) : ''; ?>
			<?php }else{?>		
				<p class="btn btn-danger align-button"> Condutor Inativo não pode ser vinculado a escola</p>
			<?php }?>		

            </div>
            <div class="box-body" style="margin-left:10px;">
                <?= GridView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->escolas,
                        'key' => 'id',
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                    ]),
                    'pjax' => true,
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        'options' => [
                            'id'=>'grid',
                        ]
                    ],
                    'options' => [
                        'class' => 'table-header-ajax',
                    ],
                    'striped' => false,
                    'bootstrap' => true,
                    'emptyText' => '<h3 class="vazio">Nenhuma escola</h3>',
                    'columns' => [
                        [
                            'attribute' => 'idEscola',
                            'label' => 'Escola',
                            'value'=>  function($model){
                                return Escola::ARRAY_TIPO[$model->escola->tipo].' '.$model->escola->nome;
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_DISTRIBUICAO])? '{cartaApresentacao} {delete}' : '{cartaApresentacao} ',
                            'buttons' => [
                                'delete' => function($url, $model){
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['condutor-escola/delete', 'id' => $model->id],
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
                                'cartaApresentacao' => function ($url, $model) {                                         
                                    if((Usuario::permissao(Usuario::PERFIL_CONDUTOR))) {
                                        return '';
                                    } else {
                                        return Html::a('<i class="fa fa-file" aria-hidden="true"></i>', Url::to(['pdf/carta-apresentacao', 'pdf' => 1, 'idCondutor' => $model->idCondutor, 'idEscola' => $model->idEscola]), ['target' => '_blank', 'title' => Yii::t('app', 'Gerar relatório'), 'data-pjax'=>0]);
                                    }
                                },
                            ]
                        ],
                    ],
                ]); ?>
		    </div>
        </div>

        <div class="box box-solid">
            <div class="box-header with-border">
                <h3>Rotas</h3>
            </div>
                 
            <div class="box-body" style="margin-left:10px;">
                <?= GridView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->vinculo,
                        'key' => 'id',
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                    ]),
                    'pjax' => true,
                    'pjaxSettings' =>[
                        'neverTimeout'=>true,
                        'options'=>[
                            'id'=>'gridVinculo',
                        ]
                    ],
                    'options' => [
                        'class' => 'table-header-ajax',
                    ],
                    'striped' => false,
                    'bootstrap' => true,
                    'emptyText' => '<h3 class="vazio">Nenhuma rota</h3>',
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'viagem',                    
                            'value'=>  function($model){
                                return $model->viagem ? CondutorRota::ARRAY_VIAGEM[$model->viagem] : '-';
                            },    
                        ],
                        'descricao',    
                        [
                            'attribute' => 'sentido',
                            'value'=>  function($model){
                                return $model->sentido ? CondutorRota::ARRAY_SENTIDO[$model->sentido] : '-';
                            },
                        ], 
                        [
                            'label' => 'Assentos livres',
                            'attribute' => 'idCondutor',        
                            'value' => function ($model) { 
                                return $model->condutor ? $model->condutor->veiculo->capacidade - count($model->alunoPonto) : '-';
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_DISTRIBUICAO])?'{view}':'',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return  Html::a('<i class="fa fa-eye" aria-hidden="true"></i>', Url::to(['condutor-rota/roterizar', 'idCondutorRota' => $model->id]), ['data-pjax' => 0,'target' => '_blank', 'title' => Yii::t('app', 'Gerar relatório'),]);
                                }
                            ]
                        ],                        
                    ],
                ]); ?>
             </div>
         </div>

         <div class="box box-solid">
            <div class="box-header with-border">
                <h3>Documentos do Condutor</h3>
            </div>
            
            <div class="box-body" style="margin-left:10px;">
                <h4> <i class="fa fa-file" aria-hidden="true"></i> CNH 
				
					  <?= Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CNH], [
                            'class' => 'btn btn-danger btn-sm pull-right align-button input-file',
                            'data' => [
                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                'method' => 'post',
                            ],
                        ]) : ''; ?>
						
                    
                </h4>
              
                <?php 
                    if ($model->docCnhCondutor) { 
                        foreach ($model->docCnhCondutor as $documento)
                        {
							if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
                            
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
					
					
                ?>
				  <hr>
            </div>

            <div class="box-body" style="margin-left:10px;">
                <h4><i class="fa fa-file" aria-hidden="true"></i> CRLV 
                    <?=  Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CNH], [
                        'class' => 'btn btn-danger btn-sm pull-right align-button',
                        'data' => [
                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                        'method' => 'post',
                        ],
                        ]) : '';
                    ?>
                </h4>
                
                <?php 
                    if ($model->docCRLV) { 
                        foreach ($model->docCRLV as $documento)
                        {
							if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
							
                           
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
                ?>
				<hr>
            </div>

            <div class="box-body" style="margin-left:10px;">
                <h4><i class="fa fa-file" aria-hidden="true"></i> Apolice de Seguro Veícular
                    <?=  Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CNH], [
                        'class' => 'btn btn-danger btn-sm pull-right align-button',
                        'data' => [
                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                        'method' => 'post',
                        ],
                        ]) : '';
                    ?>
                </h4>
               
                <?php 
                    if ($model->docApoliceSeguro) { 
                        foreach ($model->docApoliceSeguro as $documento)
                        {
                            if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
                ?>
				 <hr>
            </div>

            <div class="box-body" style="margin-left:10px;">
                <h4><i class="fa fa-file" aria-hidden="true"></i> Autorização Escolar123 (Vistoria Semestral) - DETRAN
                    <?=  Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CNH], [
                        'class' => 'btn btn-danger btn-sm pull-right align-button',
                        'data' => [
                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                        'method' => 'post',
                        ],
                        ]) : '';
                    ?>
                </h4>
               
                <?php 
                    if ($model->docAutorizacaoEscolar) { 
                        foreach ($model->docAutorizacaoEscolar as $documento)
                        {
                            if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
                ?>
				 <hr>
            </div>

            <div class="box-body" style="margin-left:10px;">
                <h4><i class="fa fa-file" aria-hidden="true"></i> Certidão de prontuário da CNH - DETRAN
                    <?=  Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CNH], [
                        'class' => 'btn btn-danger btn-sm pull-right align-button',
                        'data' => [
                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                        'method' => 'post',
                        ],
                        ]) : '';
                    ?>
                </h4>
                <hr>
                <?php 
                    if ($model->docProntuarioCNH) { 
                        foreach ($model->docProntuarioCNH as $documento)
                        {
                           if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
                ?>
				 <hr>
            </div>

        </div>
		
		<div class="box box-solid">
            <div class="box-header with-border">
                <h3>Documentos do Monitor</h3>
            </div>
            
            <div class="box-body" style="margin-left:10px;">
                <h4> <i class="fa fa-file" aria-hidden="true"></i> CNH/RG com CPF 
				
					  <?= Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_RG_MONITOR], [
                            'class' => 'btn btn-danger btn-sm pull-right align-button input-file',
                            'data' => [
                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                'method' => 'post',
                            ],
                        ]) : ''; ?>
						
                    
                </h4>
              
                <?php 
                    if ($model->docRgMonitor) { 
                        foreach ($model->docRgMonitor as $documento)
                        {
							if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
                            
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
					
					
                ?>
				  <hr>
            </div>

            <div class="box-body" style="margin-left:10px;">
                <h4><i class="fa fa-file" aria-hidden="true"></i> Contrato de Trabalho 
                    <?=  Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CONTRATO_TRABALHO], [
                        'class' => 'btn btn-danger btn-sm pull-right align-button',
                        'data' => [
                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                        'method' => 'post',
                        ],
                        ]) : '';
                    ?>
                </h4>
                
                <?php 
                    if ($model->docContratoTrabalho) { 
                        foreach ($model->docContratoTrabalho as $documento)
                        {
							if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
							
                           
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
                ?>
				<hr>
            </div>

            <div class="box-body" style="margin-left:10px;">
                <h4><i class="fa fa-file" aria-hidden="true"></i> Certidão de antecedentes criminais
                    <?=  Condutor::permissaoRemover() ? Html::a('Apagar arquivos', ['condutor/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_CERTIDAO_ANTECEDENTES_CRIMINAIS], [
                        'class' => 'btn btn-danger btn-sm pull-right align-button',
                        'data' => [
                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                        'method' => 'post',
                        ],
                        ]) : '';
                    ?>
                </h4>
               
                <?php 
                    if ($model->docCertidaoAntecedentesCriminais) { 
                        foreach ($model->docCertidaoAntecedentesCriminais as $documento)
                        {
                            if(Condutor::permissaoRemover() && Condutor::permissaoEditar()){
								$tipo = substr($documento->arquivo, -3);
								$url = Url::to(['condutor/delete-doc', 'id' => $documento->id]);
								echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
							}else{
								echo'<a target="_blank" href="'.$documento->arquivo.'">Faça o download do arquivo </a> <br> ';
							}
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Sem anexo.</div>';
                    }
                ?>
				 <hr>
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
