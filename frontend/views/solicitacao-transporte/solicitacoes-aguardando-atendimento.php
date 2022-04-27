<?php
use common\models\SolicitacaoCredito;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use common\models\Escola;
use common\models\EscolaHomologacao;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

use common\models\Condutor;
use common\models\Configuracao;
use common\models\UsuarioGrupo;
/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoTransporteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitações Aguardando Atendimento ';
$this->params['breadcrumbs'][] = 'Solicitações Aguardando Atendimento';


?>
<style>
.swal2-modal {
  min-height: 300px;
}
</style>
<link href="https://use.fontawesome.com/releases/v5.0.1/css/all.css" rel="stylesheet">
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4>
                    <?= '<span class="label label-primary">Total: '.$dataProvider->getTotalCount().'</span>'; ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'panel' => [
                        'heading'=>false,
                        'type'=>false,
                        'showFooter'=>false
                    ],
                    'summary' => "Exibindo <b>{begin}</b>-<b>{end}</b> de <b>{totalCount}</b> itens.",
                    'toolbar' => \Yii::$app->showEntriesToolbar->create(),
                    'columns' => [       
					[		
                           'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna 
                           'class' => 'yii\grid\ActionColumn',
                           'template' => '{verIrmao} {view} ',
                           'buttons' => [
						   'verIrmao' => function ($url, $model) { 
						   
									if(($model->tipoFrete == 1) or ($model->tipoFrete == 2)){
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
									
								}
						   ]
                        ],					
                        [
                            'class'=>'\kartik\grid\DataColumn',
                            'attribute'=>'id',
                            'filterInputOptions' => ['type' => 'number', 'class' => 'form-control'],
                            'contentOptions' => array('style' => 'min-width:70px;'),
                        ],
                        [
                            'attribute' => 'anoVigente',
                        ],   
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'data',
                            // 'filterType' => GridView::FILTER_DATE,
                            // 'value' => function($model) {
                            //     $d = new DateTime($model->data);
                            //     return $d->format('d/m/Y H:i');
                            // },
                            'format' => ['date', 'php:d/m/Y'],

                            // 'filterType' => GridView::FILTER_DATE_RANGE,
                            'filter' => DateRangePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'data',
                                'convertFormat' => true,
                                 'pluginEvents' => [
                                    "show.daterangepicker" => "function(ev, picker) { picker.autoUpdateInput = true; }",
                                    'cancel.daterangepicker'=>"function(ev, picker) { clearInputs2(ev); }"
                                ],
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'd/m/Y',
                                    ],
                                ],
                            ]),
                            'contentOptions' => array('style' => 'width:150px;'),
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'ultimaMovimentacao',
                            'format' => ['date', 'php:d/m/Y'],
                           
                            'filter' => DateRangePicker::widget([
                                'pluginEvents' => [
                                    "show.daterangepicker" => "function(ev, picker) { picker.autoUpdateInput = true; }",
                                    'cancel.daterangepicker'=>"function(ev, picker) { clearInputs(ev); }"
                                ],
                                'model' => $searchModel,
                                'attribute' => 'ultimaMovimentacao',
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'd/m/Y',
                                    ],
                                ],
                            ]),
                            'contentOptions' => array('style' => 'width:150px;'),
                        ],
                        [
                            'attribute' => 'novaSolicitacao',
                            'label' => 'Categoria da solicitação',
                            'value' => function($data) {
                                return $data ? SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO[$data->novaSolicitacao] : '-';
                            },
                            'filter' => SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO
                        ],
                        [
                            'attribute' => 'tipoSolicitacao',
                            'filter' => SolicitacaoTransporte::ARRAY_TIPO_SOLICITACAO,
                            'value' => function($data) {
                                return SolicitacaoTransporte::ARRAY_TIPO_SOLICITACAO[$data->tipoSolicitacao];
                            }
                        ],
                        [
                            'attribute' => 'tipoFrete',
                            'label' => 'Tipo de frete',
                            'value' => function($data) {
                                return $data->tipoFrete ? SolicitacaoTransporte::ARRAY_TIPO_FRETE[$data->tipoFrete] : '-';
                            },
                            'filter' => SolicitacaoTransporte::ARRAY_TIPO_FRETE
                        ],
                        [
                            'attribute' => 'modalidadeBeneficio',
                            'label' => 'Modalidade',
                            'value' => function($data) {
                                return $data ? Aluno::ARRAY_MODALIDADE[$data->modalidadeBeneficio] : '-';
                            },
                            'filter' => Aluno::ARRAY_MODALIDADE
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function($model){
                                return $model->status ? SolicitacaoTransporte::ARRAY_STATUS[$model->status] : '-';

                            },
                            'contentOptions' => ['style' => 'min-width:150px;'],
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
                            'value' => function($model){
                                return $model->aluno->nome;//Yii::t('app', $model->escola->nome);
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
							'attribute' => 'idade',
							'label' => 'Idade',
							'value' => function($model){
								$idade      = date("Y") - $model->aluno->dataNascimento;
								if (date("m") < $mesNasc){
									$idade -= 1;
								} elseif ((date("m") == $mesNasc) && (date("d") <= $diaNasc) ){
									$idade -= 1;
								}
								return $idade.' anos';
							}
						],
						[
                            'attribute' => 'ra',
                            'label' => 'RA',
                            'value' => function($model) {
                                return $model->aluno->RA.'-'.$model->aluno->RAdigito;
                            },
							'filter'=>'',
                        ],
						[
                            'attribute' => 'endereco',
                            'label' => 'Endereço',
                            'value' => function($model) {
                                return $model->aluno->endereco;
                            },
							'filter'=>'',
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
                            'attribute' => 'bairro',
                            'label' => 'Bairro',
                            'value' => function($model) {
                                return $model->aluno->bairro;
                            },
							'filter'=>'',
                        ],
						[
                            'attribute' => 'complementoResidencia',
                            'label' => 'Complemento',
                            'value' => function($model) {
                                return $model->aluno->complementoResidencia;
                            },
							'filter'=>'',
                        ],
						[
							'attribute' => 'serie',
							'label' => 'Ano/Série e Turma',
							'value' =>   function($model){
								return  Aluno::ARRAY_SERIES[$model->aluno->serie].'-'.Aluno::ARRAY_TURMA[$model->aluno->turma];
							},
							'filter' => Aluno::ARRAY_SERIES
						],					
						[
							'attribute' => 'horarioEntrada',
							'label' => 'Horário de Entrada',
							'value' =>   function($model){
								return $model->aluno->horarioEntrada;
							},
							
						],
						[
							'attribute' => 'horarioSaida',
							'label' => 'Horário de Saída',
							'value' =>   function($model){
								return $model->aluno->horarioSaida;
							},
							 
						],
						[
							'attribute' => 'turno',
							'label' => 'Turno',
							'value' =>   function($model){
								return Aluno::ARRAY_TURNO[$model->aluno->turno];
							},
						],
                        [
                            'headerOptions' => ['style' => 'width:200px'],
                            'attribute' => 'idEscola',
                            'value' => function($model){
                                return $model->escola->nomeCompleto;//Yii::t('app', $model->escola->nome);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => ArrayHelper::map(Escola::find()->all(), 'id', 'nomeCompleto'), 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [ 
                                'placeholder' => '-',
                                
                            ]
                        ],
                        [
                            'label' => 'Necessidades especiais',
                            'attribute' => 'necessidadeEspecial',
                            'filter' =>  [1 => 'NÃO', 2 => 'SIM'],
                            'value' => function ($model) {
                                $listaNecessidade = [];
                                foreach ($model->aluno->necessidades as $tipoNecessidade)
                                {
                                    $listaNecessidade[] = $tipoNecessidade->necessidadesEspeciais->nome;
                                }

                                return implode (',', $listaNecessidade);
                            },
                        ],
                        [
                            'headerOptions' => ['style' => 'width:200px'],
                            'attribute' => 'grupo',
                            'value' => function($model){
                               return UsuarioGrupo::grupoSolicitacao($model);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => UsuarioGrupo::ARRAY_GRUPOS_SEM_FINANCEIRO, 
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => [ 
                                'placeholder' => '-',
                                
                            ]
                        ],
                        // [
                        //     'attribute' => 'condutorIdaNome',
                        //     'label' => 'Condutor Ida',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaIda ? $model->rotaIda->condutor->nome : '-';
                        //     }
                        // ],
                        // // [
                        // //     'attribute' => 'condutorIdaAlvara',
                        // //     'label' => 'Alvará',
                        // //     'value' => function($model){
                        // //         return $model->rotaIda ? $model->rotaIda->condutor->alvara : '-';
                        // //     }
                        // // ],
                        // [
                        //     'attribute' => 'condutorIdaAlvara',
                        //     'label' => 'Alvará',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['>','alvara', 0])->all(), 'id', 'alvara'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaIda ? $model->rotaIda->condutor->alvara : '-';
                        //     }
                        // ],
                        // [
                        //     'attribute' => 'condutorIdaTelefone',
                        //     'label' => 'Telefone',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['is not', 'telefone', null])->all(), 'id', 'telefone'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaIda ? $model->rotaIda->condutor->telefone : '-';
                        //     }
                        // ],


                        // [
                        //     'attribute' => 'condutorVoltaNome',
                        //     'label' => 'Condutor Volta',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaVolta ? $model->rotaVolta->condutor->nome : '-';
                        //     }
                        // ],
                        // // [
                        // //     'attribute' => 'condutorIdaAlvara',
                        // //     'label' => 'Alvará',
                        // //     'value' => function($model){
                        // //         return $model->rotaIda ? $model->rotaIda->condutor->alvara : '-';
                        // //     }
                        // // ],
                        // [
                        //     'attribute' => 'condutorVoltaAlvara',
                        //     'label' => 'Alvará',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['>','alvara', 0])->all(), 'id', 'alvara'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaVolta ? $model->rotaVolta->condutor->alvara : '-';
                        //     }
                        // ],
                        // [
                        //     'attribute' => 'condutorVoltaTelefone',
                        //     'label' => 'Telefone',
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => ArrayHelper::map(Condutor::find()->andWhere(['is not', 'telefone', null])->all(), 'id', 'telefone'), 
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ], 
                        //     'filterInputOptions' => [
                        //         'placeholder' => '-',
                        //     ],
                        //     'value' => function($model){
                        //         return $model->rotaVolta ? $model->rotaVolta->condutor->telefone : '-';
                        //     }
                        // ],
                       
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

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
			  Texto = Texto + ' <b>Responsável </b>:'+data[0].nomePai+' <br><br>';
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

    function clearInputs(e){
        e.stopPropagation();
        e.preventDefault();
        $('#solicitacaotransportesearch-ultimamovimentacao').val(''); 
        $('input[name="daterangepicker_start"]').val("");
        $('input[name="daterangepicker_end"]').val("");
                let params =window.location.href
             params = params.split('&')
            let finalLocation = ''
            for(let i=0; i<=params.length; i++){
                let param = params[i];
                if(param && param.startsWith('SolicitacaoTransporteSearch') && !param.startsWith('SolicitacaoTransporteSearch%5BultimaMovimentacao')){
                    finalLocation += '&'+param
                }
            }
            // console.warn(finalLocation)
            window.location.href = 'index.php?r=solicitacao-transporte%2Fsolicitacoes-aguardando-atendimento'+finalLocation;

    }

        function clearInputs2(e){
        e.stopPropagation();
        e.preventDefault();
        $('#solicitacaotransportesearch-data').val(''); 
        $('input[name="daterangepicker_start"]').val("");
        $('input[name="daterangepicker_end"]').val("");
                let params =window.location.href
             params = params.split('&')
            let finalLocation = ''
            for(let i=0; i<=params.length; i++){
                let param = params[i];
                if(param && param.startsWith('SolicitacaoTransporteSearch') && !param.startsWith('SolicitacaoTransporteSearch%5Bdata')){
                    finalLocation += '&'+param
                }
            }
            // console.warn(finalLocation)
            window.location.href = 'index.php?r=solicitacao-transporte%2Fsolicitacoes-aguardando-atendimento'+finalLocation;

    }
</script>
