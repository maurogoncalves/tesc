<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Usuario;
use kartik\grid\GridView;
use kartik\dialog\Dialog;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use common\models\Escola;
use yii\data\ArrayDataProvider;
use common\models\UsuarioGrupo;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Usuario */

$this->title = 'Solicitação: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Solicitação de transporte', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="row">
    <div class="col-md-6">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3>Dados da solicitação</h3>
                    <?php
                    $flagAutorizadoGerenciar = true;

                    if(Usuario::permissao(Usuario::PERFIL_TESC_DISTRIBUICAO)){
                        if(!UsuarioGrupo::autorizarSolicitacao($model)){
                            $flagAutorizadoGerenciar = false;
                        }
                    }

                    if($flagAutorizadoGerenciar){
                        switch ($model->status) {
                            case SolicitacaoTransporte::STATUS_ANDAMENTO:
                                if (Usuario::permissao(Usuario::PERFIL_DIRETOR)) {
                                    echo '<div class="box-header with-border">';
									if($model->modalidadeBeneficio == 2){
										echo Html::button('Conceder', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR]), 'title' => 'Conceder', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
									}else{
										echo Html::button('Deferir', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
									}
										
                                    
                                    echo Html::button('Indeferir', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_INDEFERIDO]), 'title' => 'Indeferir', 'class' => 'showModalButton btn btn-danger pull-right']);
                                    echo '</div>';
                                }
                                break;

                            case SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR:
                                if (Usuario::permissoes([Usuario::PERFIL_TESC_DISTRIBUICAO, Usuario::PERFIL_TESC_PASSE_ESCOLAR, Usuario::PERFIL_SUPER_ADMIN])) {
                                    // Se for distribuicao e NÃO pertencer ao grupo especial
                                    if (Usuario::permissao(Usuario::PERFIL_TESC_DISTRIBUICAO) && !UsuarioGrupo::solicitacaoPermitida($model)) {

                                        print '<div class="alert alert-warning" role="alert">
                                        Você NÃO pertence a nenhum grupo que tem permissão para deferir ou inderir essa solicitação.
                                    </div>';
                                    } else {
                                        echo '<div class="box-header with-border">';
                                        if($model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO){
											if($model->modalidadeBeneficio == 2){
												echo Html::button('Conceder', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_CANCELADO]), 'title' => 'Conceder', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
											}else{
												echo Html::button('Receber', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_CANCELADO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
											}	
										}else{
											if($model->modalidadeBeneficio == 2){
												echo Html::button('Conceder', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_CONCEDIDO]), 'title' => 'Conceder', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
											}else{
												echo Html::button('Receber', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_DEFERIDO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
											}	
										}
                                            
                                        echo Html::button('Devolver', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_INDEFERIDO]), 'title' => 'Indeferir', 'class' => 'showModalButton btn btn-danger pull-right']);
                                        echo '</div>';
                                    }
                                }
                                if (Usuario::permissoes([Usuario::PERFIL_DRE])) {
                                    // Se for distribuicao e NÃO pertencer ao grupo especial
                                    if (Usuario::permissao(Usuario::PERFIL_TESC_DISTRIBUICAO) && !UsuarioGrupo::solicitacaoPermitida($model)) {
                                        print '<div class="alert alert-warning" role="alert">
                                        Você NÃO pertence a nenhum grupo que tem permissão para deferir ou inderir essa solicitação.
                                    </div>';
                                    } else {
                                        echo '<div class="box-header with-border">';
										
										if($model->modalidadeBeneficio == 2){
											echo Html::button('Conceder', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_CONCEDIDO]), 'title' => 'Conceder', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
										}else{
											echo Html::button('Deferir', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_DEFERIDO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
										}
                                        
										
                                        echo Html::button('Indeferir', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_INDEFERIDO]), 'title' => 'Indeferir', 'class' => 'showModalButton btn btn-danger pull-right']);
                                        echo '</div>';
                                    }
                                }
                                break;


                            case SolicitacaoTransporte::STATUS_DEFERIDO_DRE:
                                if (Usuario::permissoes([Usuario::PERFIL_TESC_DISTRIBUICAO, Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_PASSE_ESCOLAR])) {
                                    echo '<div class="box-header with-border">';
                                    if($model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO){
										if($model->modalidadeBeneficio == 2){
											echo Html::button('Conceder', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_CANCELADO]), 'title' => 'Conceder', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
										}else{
											echo Html::button('Receber', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_CANCELADO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
										}
										
									}else{
										if($model->modalidadeBeneficio == 2){
											echo Html::button('Conceder', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_CONCEDIDO]), 'title' => 'Conceder', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
										}else{
											echo Html::button('Receber', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_DEFERIDO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
										}
										
									}

                                    echo Html::button('Devolver', ['value' => Url::to(['solicitacao-transporte/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoTransporte::STATUS_INDEFERIDO]), 'title' => 'Indeferir', 'class' => 'showModalButton btn btn-danger pull-right']);
                                    echo '</div>';
                                }


                                break;
                        }
                    } else {
                        print '<div class="alert alert-warning" role="alert">Você precisa pertencer ao grupo <b>'.UsuarioGrupo::grupoSolicitacao($model).'</b> para a solicitação.</div>';
                    }
                    // return  Html::button('<i class="fa fa-fw fa-eye showModalButton"></i>', ['value' => Url::to(['aluno-rota/index-ajax','idCondutorRota' => $model->id]), 'title' => 'Solicitação', 'class' => 'showModalButton btn btn-primary pull-right']);
                    ?>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            // 'id',
                            // 'idAluno',
                            // 'idEscola',
                            // 'data',
							[
								
                                'attribute' => 'novaSolicitacao',
                                'label' => 'Sugestão de Rotas',
								'format' => 'raw',
                                'value' => function($model){
                                    
									$sqlRotas ="select distinct p.idCondutorRota,c.nome from Ponto p  join PontoAluno pp on pp.idPonto = p.id
									left join Aluno a on a.id = pp.idAluno left join CondutorRota cr on cr.id = p.idCondutorRota left join Condutor c on c.id = cr.idCondutor
									where pp.sentido = 1 and a.bairro = '".$model->aluno->bairro."' and a.turno = '".$model->aluno->turno."' and a.idEscola = '".$model->aluno->idEscola."'  ";
									$dadosRota = Yii::$app->getDb()->createCommand($sqlRotas)->queryAll();
									
									$texto = '';
									foreach($dadosRota as $rota){
										$link ="?r=condutor-rota/roterizar&idCondutorRota=".$rota['idCondutorRota'];
										$nome = $rota['nome'];
										$link = '<a target="_blank" href="'.$link.'">'.$rota['idCondutorRota'].'</a>';
										$texto .= '<span title="'.$nome.'">'.$link.'</span>, ';
									}
									
									$sqlRotas ="select distinct p.idCondutorRota,c.nome from Ponto p  join PontoAluno pp on pp.idPonto = p.id
									left join Aluno a on a.id = pp.idAluno left join CondutorRota cr on cr.id = p.idCondutorRota left join Condutor c on c.id = cr.idCondutor
									where pp.sentido = 2 and a.bairro = '".$model->aluno->bairro."' and a.turno = '".$model->aluno->turno."' and a.idEscola = '".$model->aluno->idEscola."'  ";
									$dadosRota = Yii::$app->getDb()->createCommand($sqlRotas)->queryAll();
									
									
									foreach($dadosRota as $rota){
										$link ="?r=condutor-rota/roterizar&idCondutorRota=".$rota['idCondutorRota'];
										$nome = $rota['nome'];
										$link = '<a target="_blank" href="'.$link.'">'.$rota['idCondutorRota'].'</a>';
										$texto .= '<span title="'.$nome.'">'.$link.'</span>, ';
									}
									if (Usuario::permissoes([Usuario::PERFIL_TESC_DISTRIBUICAO, Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_PASSE_ESCOLAR])) {
										return $texto;
									}	

                                },	
									
                            ],
							
							[
                                'attribute' => 'irmao',
                                'label' => 'Irmãos',
								'format' => 'raw',
                                'value' => function($model){
                                    
									if(($model->modalidadeBeneficio == 1) or ($model->modalidadeBeneficio == 2)){
										$sqlTemIrmao ='select count(*) as total,a.id
										 from SolicitacaoTransporte st  
										 join Aluno a on st.idAluno = a.id 
										 join CondutorRota c on c.id = st.idRotaIda 
										 join CondutorRota cc on cc.id = st.idRotaVolta where  a.cpfResponsavel = '.$model->aluno->cpfResponsavel.'  and st.`status` = 6 and st.idAluno <> '.$model->aluno->id;
										$sqlTemIrmao = Yii::$app->getDb()->createCommand($sqlTemIrmao)->queryAll();
										if($sqlTemIrmao[0]['total'] <> 0){	
											return 	Html::tag('span', Html::decode('<i id='.$sqlTemIrmao[0]['id'].' class="fa fa-user-times irmao" aria-hidden="true" style="color:#ff0000;top:5px!important;cursor: pointer;"></i>'));
										}
									}

                                },								
                            ],
							
                            'anoVigente',
  
                            [
                                'attribute' => 'novaSolicitacao',
                                'label' => 'Categoria da solicitação',
                                'value' => function($model){
                                    if(is_null($model->novaSolicitacao))
                                        return '-';
                                    return $model->novaSolicitacao == 1 ? 'NOVA SOLICITAÇÃO' : 'RENOVAÇÃO DE SOLICITAÇÃO';
                                }
                            ],
                            [
                                'attribute' => 'data',
                                'value' => function ($model) {
                                    return ($model->data) ? Yii::$app->formatter->asDate($model->data, 'dd/MM/Y') : '';
                                },
                                'filterType' => GridView::FILTER_DATE,
                                'filterWidgetOptions' => [
                                    'pluginOptions' => [
                                        'format' => 'dd/mm/yyyy',
                                        'autoclose' => true,
                                        'todayHighlight' => true,
                                    ]
                                ]
                            ],
                          [
                                'label' => 'Declarações Entregues',
                                'value' => function($model) {
                                   if($model->solicitacaoEscolasProximas){
                                        foreach ($model->solicitacaoEscolasProximas as $items)
                                            $meus[] = $items->escola->nome;
                                        return implode (', ', $meus);
                                    } else {
                                        return '-';
                                    }
                                }
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
                            [
                                'attribute' => 'idAluno',
                                'value' => function ($model) {
                                    return $model->aluno->nome; //Yii::t('app', $model->escola->nome);
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => ArrayHelper::map(Aluno::find()->all(), 'id', 'nome'),
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => [
                                    'placeholder' => '-',
                                ]
                            ],
                            [
                                'label' => 'Último Condutor (Ida)',
                                'value' => function ($model) {
                                    return $model->ultimoCondutorIda->nome;
                                },
                                'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                            ],
                            [
                                'label' => 'Telefone do condutor (Ida)',
                                'value' => function ($model) {
                                    return $model->ultimoCondutorIda->telefoneValido;
                                },
                                'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                            ],
                            [
                                'label' => 'Último Condutor (Volta)',
                                'value' => function ($model) {
                                    return $model->ultimoCondutorVolta->nome;
                                    // return $model->aluno->solicitacaoAtiva->rotaVolta ? $model->aluno->solicitacaoAtiva->rotaVolta->condutor->nome.' | Rota '.$model->aluno->solicitacaoAtiva->rotaVolta->id : '-';
                                },
                                'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                            ],
                            [
                                'label' => 'Telefone do condutor (Volta)',
                                'value' => function ($model) {
                                    return $model->ultimoCondutorVolta->telefoneValido;
                                },
                                'visible' => $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO && $model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE,
                            ],
                            [
                                'label' => 'RA Aluno',
                                'value' => function ($model) {
                                    return $model->aluno->RA . ' ' . $model->aluno->RAdigito; //Yii::t('app', $model->escola->nome);
                                },
                            ],
                            [
                                'label' => 'Ano/Série',
                                'value' => function ($model) {
                                    return Aluno::ARRAY_SERIES[$model->aluno->serie];
                                },
                            ],
                            [
                                'label' => 'Turma',
                                'value' => function ($model) {
                                    return Aluno::ARRAY_TURMA[$model->aluno->turma];
                                },
                            ],
                            [
                                'label' => 'Horário Entrada',
                                'attribute' => 'idAluno',
                                'value' => function ($model) {
                                    return $model->aluno->horarioEntrada; //Yii::t('app', $model->escola->nome);
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => ArrayHelper::map(Aluno::find()->all(), 'id', 'horarioEntrada'),
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => [
                                    'placeholder' => '-',
                                ]
                            ],
                            [
                                'label' => 'Horário Saída',
                                'attribute' => 'idAluno',
                                'value' => function ($model) {
                                    return $model->aluno->horarioSaida; //Yii::t('app', $model->escola->nome);
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => ArrayHelper::map(Aluno::find()->all(), 'id', 'horarioSaida'),
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => [
                                    'placeholder' => '-',
                                ]
                            ],
							[
                                'label' => 'Período',
                                'value' => function ($model) {
                                    return Aluno::ARRAY_TURNO[$model->aluno->turno];
                                },
                            ],
                            [
                                'label' => 'Endereço do Aluno',
                                'attribute' => 'idAluno',
                                'value' => function ($model) {
                                    return $model->aluno->endereco . ', ' . $model->aluno->numeroResidencia . ', ' . $model->aluno->bairro; //Yii::t('app', $model->escola->nome);
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => ArrayHelper::map(Aluno::find()->all(), 'id', 'endereco'),
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => [
                                    'placeholder' => '-',
                                ]
                            ],
							[
                            'attribute' => 'numeroResidencia',
                            'label' => 'Número',
                            'value' => function($model) {
                                return $model->aluno->numeroResidencia;
                            },
							'filter'=>'',
							],
                            [
                                'label' => 'Complemento',
                                'attribute' => 'complementoResidencia',
                                'value' => function ($model) {
                                    return $model->aluno->complementoResidencia;
                                }
                            ],
                            [
                                'attribute' => 'modalidadeBeneficio',
                                'value' => function ($model) {
                                    return Aluno::ARRAY_MODALIDADE[$model->modalidadeBeneficio];
                                },
                            ],
                            [
                                'attribute' => 'tipoFrete',
                                'label' => 'Tipo do frete',
                                'value' => function ($data) {
                                    return  $data->tipoFrete ? SolicitacaoTransporte::ARRAY_TIPO_FRETE[$data->tipoFrete] : '-';
                                },
                            ],
                            [
                                'label' => 'Necessidades especiais',
                                'value' => function ($model) {
                                    if ($model->aluno->necessidades) {
                                        foreach ($model->aluno->necessidades as $items)
                                            $meus[] = $items->necessidadesEspeciais->nome;
                                        return implode(', ', $meus);
                                    } else {
                                        return '-';
                                    }
                                }
                            ],
                            [
                                'attribute' => 'idEscola',
                                'value' => function ($model) {
                                    return $model->escola->nomeCompleto; //Yii::t('app', $model->escola->nome);
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => ArrayHelper::map(Escola::find()->all(), 'id', 'nome'),
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => [
                                    'placeholder' => '-',

                                ]
                            ],
                            'justificativaBarreiraFisica:ntext',
                            [
                                'attribute' => 'cartaoPasseEscolar',
                                'format' => 'raw',
                                'value' => function($model) {
                                    if (!Usuario::permissao(Usuario::PERFIL_TESC_PASSE_ESCOLAR))
                                        return '<div id="val-cartao-pe">'.$model->cartaoPasseEscolar.'</div><div class="input-group margin" style="display:none;" id="cartao-pe"><input id="new-cartao-pe" type="number" class="form-control"><span class="input-group-btn"><button type="button" id="save-cartao-pe" class="btn btn-success btn-flat">Salvar</button></span></div><button id="update-cartao-pe" class="btn btn-primary btn-sm margin pull-right"><span class="glyphicon glyphicon-pencil"></span></button>';
                                    else
                                        return $model->cartaoPasseEscolar;
                                }
                            ],
                            [
                                'attribute' => 'cartaoValeTransporte',
                                'format' => 'raw',
                                'value' => function($model) {
                                    if (!Usuario::permissao(Usuario::PERFIL_TESC_PASSE_ESCOLAR))
                                        return '<div id="val-cartao-vt">'.$model->cartaoValeTransporte.'</div><div class="input-group margin" style="display:none;" id="cartao-vt"><input id="new-cartao-vt" type="number" class="form-control"><span class="input-group-btn"><button type="button" id="save-cartao-vt" class="btn btn-success btn-flat">Salvar</button></span></div><button id="update-cartao-vt" class="btn btn-primary btn-sm margin pull-right"><span class="glyphicon glyphicon-pencil"></span></button>';
                                    else
                                        return $model->cartaoValeTransporte;
                                }
                            ],
                            [
                                'attribute' => 'barreiraFisica',
                                'format' => 'raw',
                                'value' => function($model) {
                                    if (!Usuario::permissao(Usuario::PERFIL_TESC_PASSE_ESCOLAR))
                                        return '<div id="val-barreira-fisica">'.($model->barreiraFisica == 1 ? 'SIM' : 'NÃO').'</div><div class="input-group margin" style="display:none;" id="barreira-fisica"><select id="new-barreira-fisica" type="text" class="form-control"><option value="1">SIM</option><option value="0">NÃO</option></select><span class="input-group-btn"><button type="button" id="save-barreira-fisica" class="btn btn-success btn-flat">Salvar</button></span></div><button id="update-barreira-fisica" class="btn btn-primary btn-sm margin pull-right"><span class="glyphicon glyphicon-pencil"></span></button>';
                                    else
                                        return $model->barreiraFisica == 1 ? 'SIM' : 'NÃO';
                                }
                            ],
                            [
                                'attribute' => 'motivoBarreiraFisica',
                                'format' => 'raw',
                                'value' => function($model) {
                                    if (!Usuario::permissao(Usuario::PERFIL_TESC_PASSE_ESCOLAR))
                                        return '<div id="val-motivo-barreira-fisica">'.$model->motivoBarreiraFisica.'</div><div class="input-group margin" style="display:none;" id="motivo-barreira-fisica"><input id="new-motivo-barreira-fisica" type="text" class="form-control"><span class="input-group-btn"><button type="button" id="save-motivo-barreira-fisica" class="btn btn-success btn-flat">Salvar</button></span></div><button id="update-motivo-barreira-fisica" class="btn btn-primary btn-sm margin pull-right"><span class="glyphicon glyphicon-pencil"></span></button>';
                                    else
                                        return $model->motivoBarreiraFisica;
                                }
                            ],
                            [
                                'attribute' => 'distanciaEscola',
                                'format' => 'raw',
								'label' => 'Distância em metros',
                                'value' => function($model) {
                                    if (!Usuario::permissao(Usuario::PERFIL_TESC_PASSE_ESCOLAR))
                                        return '<div id="val-distancia-escola">'.(($model->distanciaEscola) ? $model->distanciaEscola . ' ' : '-').'</div><div class="input-group margin" style="display:none;" id="distancia-escola"><input id="new-distancia-escola" type="number" class="form-control"><span class="input-group-btn"><button type="button" id="save-distancia-escola" class="btn btn-success btn-flat">Salvar</button></span></div><button id="update-distancia-escola" class="btn btn-primary btn-sm margin pull-right"><span class="glyphicon glyphicon-pencil"></span></button>';
                                    else
                                        return (($model->distanciaEscola) ? $model->distanciaEscola . ' ' : '-').'</div>';
                                }
                            ],
                            [
                                'attribute' => 'checkForm',
                                'value' => function ($model){
                                    return $model->checkForm ? 'SIM' : 'NÃO';
                                }
                             ],
                             [
                                'attribute' => 'checkInex',
                                'value' => function ($model){
                                    return $model->checkInex ? 'SIM' : 'NÃO';
                                }
                             ], 
                             [
                                'attribute' => 'checkEnd',
                                'value' => function ($model){
                                    return $model->checkEnd ? 'SIM' : 'NÃO';
                                }
                             ],
                             [
                                'attribute' => 'checkMemorando',
                                'value' => function ($model){
                                    return $model->checkMemorando ? 'SIM' : 'NÃO';
                                }
                             ],
                             [
                                'attribute' => 'checkSed',
                                'value' => function ($model){
                                    return $model->checkSed ? 'SIM' : 'NÃO';
                                }
                             ],
                             [
                                'attribute' => 'checkVizinho',
                                'value' => function ($model){
                                    return $model->checkVizinho ? 'SIM' : 'NÃO';
                                }
                             ], 
                             [
                                'attribute' => 'checkLaudoMedico',
                                'value' => function ($model){
                                    return $model->checkLaudoMedico ? 'SIM' : 'NÃO';
                                }
                             ], 
                             [
                                'attribute' => 'checkSolicitacaoEspecial',
                                'value' => function ($model){
                                    return $model->checkSolicitacaoEspecial ? 'SIM' : 'NÃO';
                                }
                             ], 
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="col-md-12">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3>Histórico de status</h3>
            </div>
            <div class="box-body">
                <?php
        $tratados =[];
        $historicosTratados = [];
        foreach($model->historico as $historico) {
            if(!isset($tratado[$historico->justificativa.' '.$historico->dataCadastro])) {
                $historicosTratados[] = $historico;
                $tratado[$historico->justificativa.' '.$historico->dataCadastro] = $historico->id;
            } 
        }


                 ?>
                <?= GridView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $historicosTratados,
                        'key' => 'id',
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                    ]),
                    'pjax' => true,
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        'options' => [
                            'id' => 'grid',
                        ]
                    ],
                    'options' => [
                        'class' => 'table-header-ajax',
                    ],
                    'summary' => '',
                    'striped' => false,
                    'bootstrap' => true,
                    'emptyText' => '<h3 class="vazio">Nenhum status</h3>',
                    'columns' => [
                        [
                            'attribute' => 'dataCadastro',
                            'label' => 'Data',
                            'value' => function ($model, $index, $widget) {
                                return ($model->dataCadastro) ? date("d/m/Y", strtotime($model->dataCadastro)): '';
                            },
                            'filterType' => GridView::FILTER_DATE,
                            'filterWidgetOptions' => [
                                'pluginOptions' => [
                                    'format' => 'dd/mm/yyyy',
                                    'autoclose' => true,
                                    'todayHighlight' => true,
                                ]
                            ]
                        ],
                        [
                            'attribute' => 'idUsuario',
                            'label' => 'Usuário',
                            'value' =>  function ($model) {
                                return $model->usuario->nome;
                            },
                        ],
                        [
                            'attribute' => 'justificativa',
                            'label' => 'Justificativa',
                            'format' => 'html',
                            'filter' => false,
                            'value'=>  function($model){
                                // if(substr( $model->justificativa, 0, 10 ) == "ATRIBUÍDO" && $model->idCondutorRota){
                                //     $model->justificativa = 'ATRIBUÍDO EM ROTA #'.$model->idCondutorRota.' DO CONDUTOR(A) '.$model->condutorRota->condutor->nome;
                                // }
                                // if(substr( $model->justificativa, 0, 8 ) == "REMOVIDO" && $model->idCondutorRota){
                                //     $model->justificativa = 'REMOVIDO DA ROTA #'.$model->idCondutorRota.' DO CONDUTOR(A) '.$model->condutorRota->condutor->nome;
                                // }
                                $response = $model->justificativa;
                                if($model->justificativaSetor)
                                    $response .= "<br><b>Justificativa do setor: </b>".$model->justificativaSetor; 
                                return $response;
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'label' => 'Status',
                            'filter' => false,
                            'value' => function ($model) {
                                return $model->status ?  SolicitacaoTransporte::ARRAY_STATUS[$model->status] : '-';
                            }
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
                        // [
                        //        'class' => 'yii\grid\ActionColumn',
                        //        'template' => '{view}',
                        //        'buttons' => [
                        //              'view' => function ($url, $model) {
                        //                 return Html::a('<i class="fa fa-fw fa-trash"></i>', ['condutor-escola/delete', 'id' => $model->id],['data' => ['method' => 'post',]])
                        //                 ;

                        //             },

                        //         ]
                        // ],


                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<script>
$( document ).ready(function() {
var btnUpdatePE = '';
var btnUpdateVT = '';
    $( "#update-cartao-pe").click(function () {
        $(this).hide();
        $('#cartao-pe').show();
    })

    $( "#update-cartao-vt").click(function () {
        $(this).hide();
        $('#cartao-vt').show();
    })

    $( "#update-barreira-fisica").click(function () {
        $(this).hide();
        $('#barreira-fisica').show();
    })

    $( "#update-motivo-barreira-fisica").click(function () {
        $(this).hide();
        $('#motivo-barreira-fisica').show();
    })

    $( "#update-distancia-escola").click(function () {
        $(this).hide();
        $('#distancia-escola').show();
    })

    $( "#save-cartao-pe").click(function () {
        $.ajax({
            type: 'POST',
            url: 'index.php?r=solicitacao-transporte%2Fupdate-cartao-passe-escolar-ajax&idSolicitacao='+<?= $model->id ?>,
            data: {
                cartaoPasseEscolar: $("#new-cartao-pe").val()
            },
        }).done(function(data) {
            if (data.status) {
                console.log(data)
                $('#cartao-pe').hide();
                $('#update-cartao-pe').show();
                $("#val-cartao-pe").text($("#new-cartao-pe").val())
                $("#new-cartao-pe").val('')
                return Swal.fire(
                    '',
                    'Cartão de passe escolar atualizado com sucesso',
                    'success'
                )
            }
            else
            {
                return Swal.fire(
                    'Ops!',
                    'Algo de errado aconteceu ao atualizar o valor do campo. Por favor, verifique a informação e tente novamente.',
                    'error'
                )
            }
        })
    })

    $( "#save-cartao-vt").click(function () {
        $.ajax({
            type: 'POST',
            url: 'index.php?r=solicitacao-transporte%2Fupdate-cartao-vale-transporte-ajax&idSolicitacao='+<?= $model->id ?>,
            data: {
                cartaoValeTransporte: $("#new-cartao-vt").val()
            },
        }).done(function(data) {
            if (data.status) {
                console.log(data)
                $('#cartao-vt').hide();
                $('#update-cartao-vt').show();
                $("#val-cartao-vt").text($("#new-cartao-vt").val())
                $("#new-cartao-vt").val('')
                return Swal.fire(
                    '',
                    'Cartão de vale transporte atualizado com sucesso',
                    'success'
                )
            }
            else
            {
                return Swal.fire(
                    'Ops!',
                    'Algo de errado aconteceu ao atualizar o valor do campo. Por favor, verifique a informação e tente novamente.',
                    'error'
                )
            }
        })
    })

    $( "#save-barreira-fisica").click(function () {
        $.ajax({
            type: 'POST',
            url: 'index.php?r=solicitacao-transporte%2Fupdate-barreira-fisica-ajax&idSolicitacao='+<?= $model->id ?>,
            data: {
                barreiraFisica: $("#new-barreira-fisica").val()
            },
        }).done(function(data) {
            if (data.status) {
                console.log(data)
                $('#barreira-fisica').hide();
                $('#update-barreira-fisica').show();
                $("#val-barreira-fisica").text(($("#new-barreira-fisica").val() == 1) ? 'SIM' : 'NÃO')
                $("#new-barreira-fisica").val('')
                return Swal.fire(
                    '',
                    'Barreira física atualizado com sucesso',
                    'success'
                )
            }
            else
            {
                return Swal.fire(
                    'Ops!',
                    'Algo de errado aconteceu ao atualizar o valor do campo. Por favor, verifique a informação e tente novamente.',
                    'error'
                )
            }
        })
    })

    $( "#save-motivo-barreira-fisica").click(function () {
        $.ajax({
            type: 'POST',
            url: 'index.php?r=solicitacao-transporte%2Fupdate-motivo-barreira-fisica-ajax&idSolicitacao='+<?= $model->id ?>,
            data: {
                motivoBarreiraFisica: $("#new-motivo-barreira-fisica").val()
            },
        }).done(function(data) {
            if (data.status) {
                console.log(data)
                $('#motivo-barreira-fisica').hide();
                $('#update-motivo-barreira-fisica').show();
                $("#val-motivo-barreira-fisica").text($("#new-motivo-barreira-fisica").val())
                $("#new-motivo-barreira-fisica").val('')
                return Swal.fire(
                    '',
                    'Motivo da barreira física atualizado com sucesso',
                    'success'
                )
            }
            else
            {
                return Swal.fire(
                    'Ops!',
                    'Algo de errado aconteceu ao atualizar o valor do campo. Por favor, verifique a informação e tente novamente.',
                    'error'
                )
            }
        })
    })

    $( "#save-distancia-escola").click(function () {
        $.ajax({
            type: 'POST',
            url: 'index.php?r=solicitacao-transporte%2Fupdate-distancia-escola-ajax&idSolicitacao='+<?= $model->id ?>,
            data: {
                distanciaEscola: $("#new-distancia-escola").val()
            },
        }).done(function(data) {
            if (data.status) {
                console.log(data)
                $('#distancia-escola').hide();
                $('#update-distancia-escola').show();
                
                $("#val-distancia-escola").text($("#new-distancia-escola").val()+' KM')
                $("#new-distancia-escola").val('')
                return Swal.fire(
                    '',
                    'Distância da escola atualizada com sucesso',
                    'success'
                )
            }
            else
            {
                return Swal.fire(
                    'Ops!',
                    'Algo de errado aconteceu ao atualizar o valor do campo. Por favor, verifique a informação e tente novamente.',
                    'error'
                )
            }
        })
    })

});

$(document).on('click', '.irmao', function () {
	var id = $(this).attr('id');
	$.ajax({	
		type: 'POST',
		url: 'index.php?r=solicitacao-transporte/irmao',
		dataType: 'json', /* Tipo de transmissão */			
		data:{
			  aluno: id
		},
		}).done(function(data) {
			
			var Texto = "";
			  Texto = Texto + '<p align="left"> <b>Mãe </b> :'+data[0].nomeMae+' <br>';
			  Texto = Texto + ' <b>Pai </b>:'+data[0].nomePai+' <br><br>';
			  Texto = Texto + ' <b>Irmão(a) </b>:'+data[0].nome+' <br><br>';
			  if(data[0].idRotaIda){
				Texto = Texto + ' <b>Rota Ida </b>:'+data[0].idRotaIda+' <br>';  
			  }else{
				  Texto = Texto + ' <b>Rota Ida </b>: Sem rota definida <br>';
			  }			  
			  if(data[0].descricao_ida){
				 Texto = Texto + ' <b>Descrição Rota Ida </b>:'+data[0].descricao_ida+' <br><br>';
			  }
			  if(data[0].idRotaVolta){
				Texto = Texto + ' <b>Rota Volta </b>:'+data[0].idRotaVolta+' <br>';  
			  }else{
				  Texto = Texto + ' <b>Rota Volta </b>: Sem rota definida <br>';
			  }
			  
			  if(data[0].descricao_volta){
				Texto = Texto + ' <b>Descrição Rota Volta </b>:'+data[0].descricao_volta+' </p>';
			  }
			  
			  
			  
  
			Swal.fire({
				width: '600px',
				title: 'Atenção usuário(a) esse aluno tem irmão(a)',
				 html: Texto,
				//text: "Mãe: "+data[0].nomeMae+" \n Pai: "+data[0].nomePai+" \n Irmão(a): "+data[0].nome+" \n Rota Ida: "+data[0].idRotaIda+" \n Descrição Rota Ida: "+data[0].descricao_ida+" \n Rota Volta: "+data[0].idRotaVolta+" \n Descrição Rota Volta: "+data[0].descricao_volta,
				icon: 'warning',
				showCancelButton: false,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Ok',
			}).then((result) => {
				console.log('OK')
			});
						
	});
});

</script>


<?php
echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
]);
?>