<?php

use yii\helpers\Html;
// use kartik\detail\DetailView;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

use common\models\Escola;
use common\models\Usuario;
use kartik\dialog\Dialog;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\Escola */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Escolas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="titulo">Informações atuais da escola</h3>
                <span class="botoes">
                     <?= Escola::permissaoRemover() ? Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) : ''; ?>
                    
                    <?=  Escola::permissaoEditar() ? Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ']) : ''; ?>
                   
                </span>
                </div>
                 <div class="box-body">

                 <?= 
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        // 'id',
                         [
                         'attribute' => 'tipo',
              
                         'value'=>  $model->tipo ? Escola::ARRAY_TIPO[$model->tipo] : '-',
                        ],
                        'nome',
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
                         'telefone2',
                        'email:email',
                        'codigoCie',
                        [
                            'label' => 'Secretários',
                            'attribute' => 'secretarios',
                            'filter' =>  ArrayHelper::map(Usuario::find()->all(), 'id', 'nome'),
                            'format' => 'raw',
                            'value' => function($data) {
                                // $lista = [];
                                // foreach ($data->secretarios as $usuario)
                                // {
                                //     $lista[] = $usuario->usuario->nome;
                                // }
                                // return implode(',', $lista);
                                $lista = '';
                                foreach ($data->secretarios as $i => $secretario)
                                {
                                    if ($i > 0)
                                        $lista .= '<hr>';

                                    $lista .= $secretario->usuario->nome.'<br>';
                                    $lista .= '<b>CPF: </b>'.\Yii::$app->formatter->asCpf($secretario->usuario->cpf).'<br>';
                                    $lista .= '<b>RG: </b>'.\Yii::$app->formatter->asCpf($secretario->usuario->rg).'<br>';
                                    $lista .= '<b>E-mail: </b>'.$secretario->usuario->email;
                                }
                                return $lista;
                            },
                        ],
                        [
                            'label' => 'Diretores',
                            'attribute' => 'diretores',
                            'filter' =>  ArrayHelper::map(Usuario::find()->all(), 'id', 'nome'),
                            'format' => 'raw',
                            'value' => function($data) {
                                // $lista = [];
                                // foreach ($data->diretores as $usuario)
                                // {
                                //     $lista[] = $usuario->usuario->nome;
                                // }
                                // return implode(',', $lista);
                                $lista = '';
                                foreach ($data->diretores as $i => $diretor)
                                {
                                    if ($i > 0)
                                        $lista .= '<hr>';

                                    $lista .= $diretor->usuario->nome.'<br>';
                                    $lista .= '<b>CPF: </b>'.\Yii::$app->formatter->asCpf($diretor->usuario->cpf).'<br>';
                                    $lista .= '<b>RG: </b>'.\Yii::$app->formatter->asCpf($diretor->usuario->rg).'<br>';
                                    $lista .= '<b>E-mail: </b>'.$diretor->usuario->email;
                                }
                                return $lista;
                            },
                        ],
                        ],
                    ])

                 // DetailView::widget([
                 //    'model' => $model,
                 //    'striped' => false, 
                 //    'attributes' => [
                 //        // 'id',
                 //         [
                 //         'attribute' => 'tipo',
              
                 //         'value'=>  $model->tipo ? Escola::ARRAY_TIPO[$model->tipo] : '-',
                 //        ],
                 //        'nome',
                 //        'endereco',
                 //        // 'lat',
                 //        // 'lng',
                 //        'telefone',
                 //        'email:email',
                 //        'codigoCie',
                 //    ],
                 //    ])
                 ?>
            </div>
        </div>
    </div>
    <?php if(Usuario::permissoes([Usuario::PERFIL_DRE,Usuario::PERFIL_SUPER_ADMIN]) && $model->homologacao): ?>
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="titulo">Solicitação de alteração de informações

                <?php 
                echo Html::a('Aprovar alteração', ['escola/aprovacao-informacoes', 'id' => $model->id, 'status' => 1], [
                    'class' => 'btn btn-success pull-right align-button',
                    'data' => [
                    'confirm' => 'Tem certeza que aprovar a alteração das informações?',
                    'method' => 'post',
                    ],
                    ]
                );
                 
                ?>
                <?php 
                echo Html::a('Reprovar alteração', ['escola/aprovacao-informacoes', 'id' => $model->id, 'status' => 2], [
                    'class' => 'btn btn-danger pull-right align-button',
                    'data' => [
                    'confirm' => 'Tem certeza que reprovar a alteração das informações?',
                    'method' => 'post',
                    ],
                    ]
                );
                 
                ?>
                </h3>
                
                </div>
                 <div class="box-body">
               
         
                 <?= DetailView::widget([
                    'model' => $model->homologacao,
                    'attributes' => [
                        [
                            'attribute' => 'tipo',
                 
                            'value'=>  $model->tipo ? Escola::ARRAY_TIPO[$model->tipo] : '-',
                           ],
                           'nome',
                           'endereco',
                           // 'lat',
                           // 'lng',
                           'telefone',
                            'telefone2',
                           'email:email',
                           'codigoCie',
                  
                       
                        ],
                    ])
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php 
    echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
    ]);
?>