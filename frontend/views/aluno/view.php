<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;

use kartik\dialog\Dialog;
use kartik\grid\GridView;
use kartik\widgets\FileInput;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use common\models\TipoDocumento;
use common\models\Usuario;

/* @var $this yii\web\View */
/* @var $model common\models\Aluno */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Alunos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3>Dados do aluno</h3>
                    <span>
                        <?= Aluno::permissaoRemover() ? Html::a('Apagar', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger pull-right align-button',
                            'data' => [
                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                'method' => 'post',
                            ],
                        ]) : ''; ?>

                        <?= Aluno::permissaoEditar() ? Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ']) : ''; ?>
                    </span>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            // 'id',
                            // [
                            //     'label' => 'Atribuído a uma rota',
                            //     'value' => function ($model) {
                            //         return $model->alunoPonto ? 'Sim' : 'Não';
                            //     }
                            // ],
                            [
                                'label' => 'Nome do condutor (Ida)',
                                'value' => function ($model) {
                                    return $model->solicitacaoAtiva->rotaIda ? $model->solicitacaoAtiva->rotaIda->condutor->nome.' '.$model->solicitacaoAtiva->rotaIda->id : '-';
                                }
                            ],
                            [
                                'label' => 'Alvará do condutor (Ida)',
                                'value' => function ($model) {
                                    return $model->solicitacaoAtiva->rotaIda ? $model->solicitacaoAtiva->rotaIda->condutor->alvara : '-';
                                }
                            ],
                            [
                                'label' => 'Telefone do condutor (Ida)',
                                'value' => function ($model) {
                                    return $model->solicitacaoAtiva->rotaIda ?  Yii::$app->formatter->asTelefone($model->solicitacaoAtiva->rotaIda->condutor->telefone) : '-';
                                }
                            ],
                            [
                                'label' => 'Nome do condutor (Volta)',
                                'value' => function ($model) {
                                    return $model->solicitacaoAtiva->rotaVolta ? $model->solicitacaoAtiva->rotaVolta->condutor->nome.' '.$model->solicitacaoAtiva->rotaVolta->id : '-';
                                }
                            ],
                            [
                                'label' => 'Alvará do condutor (Volta)',
                                'value' => function ($model) {
                                    return $model->solicitacaoAtiva->rotaVolta ? $model->solicitacaoAtiva->rotaVolta->condutor->alvara : '-';
                                }
                            ],
                            [
                                'label' => 'Telefone do condutor (Volta)',
                                'value' => function ($model) {
                                    return $model->solicitacaoAtiva->rotaVolta ? Yii::$app->formatter->asTelefone($model->solicitacaoAtiva->rotaVolta->condutor->telefone) : '-';
                                }
                            ],
                            [
                                'label' => 'CPF',
                                'attribute' => 'CpfFormatado'
                            ],
                            [
                                'label' => 'Escola',
                                'attribute' =>  function ($model) {
                                    return $model->escola->nome;
                                },
                            ],
                            [
                                'label' => 'Data de nascimento',
                                'attribute' =>  function ($model) {
                                    return ($model->dataNascimento && $model->dataNascimento != '0000-00-00') ? date("d/m/Y", strtotime($model->dataNascimento)) : '-';
                                },
                            ],

                            'nomeMae',
                            'nomePai',
                            [
                                'label' => 'RA Aluno',
                                'value' => function ($model) {
                                    return $model->RA . ' ' . $model->RAdigito; //Yii::t('app', $model->escola->nome);
                                },
                            ],
                            // 'endereco',
                            [
                                'attribute' => 'cep',
                                'value' => function ($model) {
                                    return $model->cep;
                                }
                            ],
                            [
                                'attribute' => 'cidade',
                                'value' => function ($model) {
                                    return $model->cidade;
                                }
                            ],
                            [
                                'attribute' => 'endereco',
                                'value' => function ($model) {
                                    $endereco  = $model->tipoLogradouro ? $model->tipoLogradouro . ' ' . $model->endereco : $model->endereco;
                                    if ($model->numeroResidencia)
                                        $endereco .= ', Nº ' . $model->numeroResidencia;
                                    return $endereco;
                                }
                            ],
                            [
                                'attribute' => 'bairro',
                                'value' => function ($model) {
                                    return $model->bairro;
                                }
                            ], 
                            [
                                'attribute' => 'complementoResidencia',
                                'value' => function ($model) {
                                    return $model->complementoResidencia;
                                }
                            ],

                            // [
                            // 'label' => 'Modalidade do benefício',
                            // 'attribute'=>  function($model){
                            //     return $model->modalidadeBeneficio ? Aluno::ARRAY_MODALIDADE[$model->modalidadeBeneficio] : '-';

                            // },
                            // ],

                            // 'cartaoValeTransporte',
                            // 'cartaoPasseEscolar',
                            // // 'horarioEntrada',
                            // 'horarioSaida',
                            // 'distanceEscola',
                            // [
                            // 'label' => 'Barreira Física',
                            // 'attribute'=>  function($model){
                            //     return $model->barreiraFisica ? Aluno::ARRAY_BARREIRA_FISICA[$model->barreiraFisica] : '-';

                            // },
                            // ],
                            // 'idRgAluno',
                            // 'idComprovanteEndereco',
                            // 'idRgResponsavel',
                            // 'idDeclaracaoVizinhos',
                            // 'idLaudoMedico',
                            // 'idTransporteEspecialAdaptado',
                            // 'idDeclaracaoInexistenciaVaga',
                            'telefoneResidencial',
                            'telefoneResidencial2',
                            'telefoneCelular',
                            'telefoneCelular2',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">

                    <h3>Documentos</h3>
                </div>
                <div class="box-body" style="margin-left:10px;">

                    <div class="row margin-bottom">
                        <div class="box-header with-border">

                            <h4><i class="fa fa-file" aria-hidden="true"></i> RG do aluno
                                <?= $model->docRgAluno ? Html::a('Apagar arquivos', ['aluno/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_RG_ALUNO], [
                                    'class' => 'btn btn-danger pull-right align-button',
                                    'data' => [
                                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                                        'method' => 'post',
                                    ],
                                ]) : ''; ?>
                            </h4>
                        </div>
                        <?php if ($model->docRgAluno) {
                            foreach ($model->docRgAluno as $documento) {
                                $tipo = substr($documento->arquivo, -3);
                                $url = Url::to(['aluno/delete-doc', 'id' => $documento->id]);
                                echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);
                            }
                        } else {
                            echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Sem anexo.</div></div>';
                        }  ?>
                    </div>
                    <div class="row margin-bottom" style="background: #f9f9f9">

                        <div class="box-header with-border">
                            <h4><i class="fa fa-file" aria-hidden="true"></i> RG do responsável
                                <?= $model->docRgResponsavel ? Html::a('Apagar arquivos', ['aluno/arquivos', 'id' => $model->id, 'tipo' => TipoDocumento::TIPO_RG_RESPONSAVEL], [
                                    'class' => 'btn btn-danger pull-right align-button',
                                    'data' => [
                                        'confirm' => 'Tem certeza que deseja apagar arquivos?',
                                        'method' => 'post',
                                    ],
                                ]) : ''; ?>
                            </h4>
                        </div>
                        <?php if ($model->docRgResponsavel) {
                            foreach ($model->docRgResponsavel as $documento) {
                                $tipo = substr($documento->arquivo, -3);
                                $url = Url::to(['aluno/delete-doc', 'id' => $documento->id]);
                                echo Yii::$app->fileThumb->display($documento->arquivo, $tipo, $url);  
                            }
                        } else {
                            echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Sem anexo.</div></div>';
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3>Solicitação de transporte</h3>
            </div>
            <div class="box-header with-border">
                <?= SolicitacaoTransporte::permissaoCriar() ?  Html::button('Cancelar benefício', ['value' => Url::to(['solicitacao-transporte/create-ajax', 'idAluno' => $model->id, 'idEscola' => $model->idEscola, 'tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO]), 'title' => 'Cancelar Solicitação', 'class' => 'showModalButton btn btn-danger pull-right', 'style' => 'margin-left: 5px;']) : ''; ?>
                <?= SolicitacaoTransporte::permissaoCriar() ?  Html::button('RENOVAÇÃO / Nova Solicitação', ['value' => Url::to(['solicitacao-transporte/create-ajax', 'idAluno' => $model->id, 'idEscola' => $model->idEscola, 'tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO]), 'title' => 'Nova Solicitação', 'class' => 'showModalButton btn btn-success pull-right']) : ''; ?>

            </div>


            <div class="box-body" style="margin-left:10px;">
                <?= GridView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->solicitacoes,
                        'key' => 'id',
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                    ]),
                    'pjax' => true,
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        'options' => [
                            'id' => 'gridSolicitacoes',
                        ]
                    ],
                    'options' => [
                        'class' => 'table-header-ajax',
                    ],
                    'striped' => false,
                    'bootstrap' => true,
                    // 'summary' => "Mostrando de {begin} a {end} de {totalCount}",
                    'emptyText' => '<h3 class="vazio">Nenhuma Solicitação</h3>',
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'label' => 'Código'
                        ],
                        [
                            'attribute' => 'anoVigente'
                        ],
                        [
                            'attribute' => 'tipoSolicitacao',
                            'value' => function ($data) {
                                return ($data->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO) ? 'SOLICITAÇÃO' : 'CANCELAMENTO';
                            }
                        ],
                        [
                            'attribute' => 'data',
                            'value' => function ($model, $index, $widget) {
                                return ($model->data) ? Yii::$app->formatter->asDate($model->data, 'dd/MM/Y') : '';
                            },
                            'filter' => false
                        ],

                        [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return $model->status ? SolicitacaoTransporte::ARRAY_STATUS[$model->status] : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' =>  SolicitacaoTransporte::ARRAY_STATUS,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                        // [
                        //     'attribute' => 'idEscola',
                        //     'filter' => false
                        // ],
                        [
                            'attribute' => 'idEscola',
                            'label' => 'Escola',
                            'value' => function ($model) {
                                return $model->escola->nome; //Yii::t('app', $model->escola->nome);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [
                                'placeholder' => '-',
                            ]
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}  {encerrar} {devolver} {excluir}' ,
                            'contentOptions' => Usuario::r() ?  array('style' => 'min-width:150px;') : '',

                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return Html::button('<i class="fa fa-fw fa-eye"></i>', ['value' => Url::to(['solicitacao-transporte/view-ajax', 'id' => $model->id]), 'title' => 'Solicitação', 'class' => 'showModalButton btn btn-primary bth-xs']);
                                },
                                'encerrar' => function ($url, $model) { 
                                    if(!Usuario::r())
                                        return '';
                                    if(($model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE && $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO && $model->status == SolicitacaoTransporte::STATUS_ATENDIDO) ||
                                       ($model->modalidadeBeneficio == Aluno::MODALIDADE_PASSE && $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO && $model->status == SolicitacaoTransporte::STATUS_DEFERIDO))
                                    return ''.Html::button('Encerrar', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax-admin', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_ENCERRADA, 'alterarDadosProtegidos' => true]), 'title' => 'Encerrar', 'class' => 'showModalButton  align-button btn btn-warning']);
                                },
                                'devolver' => function ($url, $model) {
                                    if(!Usuario::r())
                                        return '';
                                    if(($model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE && $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO && $model->status == SolicitacaoTransporte::STATUS_ATENDIDO) ||
                                       ($model->modalidadeBeneficio == Aluno::MODALIDADE_PASSE && $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO && $model->status == SolicitacaoTransporte::STATUS_DEFERIDO))
                                    return ''.Html::button('Devolver', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax-admin', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_INDEFERIDO, 'alterarDadosProtegidos' => true]), 'title' => 'Devolver', 'class' => 'showModalButton  align-button btn btn-default']);
                                },
                                'excluir' => function ($url, $model) {
                                    if(!Usuario::r())
                                        return '';
                                        return Html::a('Excluir', ['solicitacao-transporte/excluir-por-admin', 'id' => $model->id], [
                                            'class' => 'btn btn-danger',
                                            'data' => [
                                                'confirm' => 'Você tem absoluta certeza disso?',
                                                'method' => 'post',
                                            ],
                                        ]);
                             
                                }

                            ]
                        ],
						[
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{alterar}' ,
                            'contentOptions' => Usuario::r() ?  array('style' => 'min-width:150px;') : '',

                            'buttons' => [                               
								'alterar' => function ($url, $model) {
									
									if($model->modalidadeBeneficio == Aluno::MODALIDADE_PASSE){
										$frase ='Voltar p/ concedido';
										$novoStatus = SolicitacaoTransporte::STATUS_CONCEDIDO;
									}else{
										$frase ='Voltar p/ recebido';
										$novoStatus = SolicitacaoTransporte::STATUS_DEFERIDO;
									}
									
									if(($model->status <> 3) && (Yii::$app->user->identity->idPerfil == 1)){
										return Html::button($frase, ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax-admin', 'id' => $model->id, 'status' => $novoStatus, 'alterarDadosProtegidos' => true]), 'title' => $frase, 'class' => 'showModalButton  btn btn-success pull-right']);
									} 	
										
                                },

                            ]
                        ],
                    ],
                ]); ?>
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


<!-- echo Html::button('Indeferir', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_INDEFERIDO]), 'title' => 'Indeferir', 'class' => 'showModalButton btn btn-danger pull-right']); -->
