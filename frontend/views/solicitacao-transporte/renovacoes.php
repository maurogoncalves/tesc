<?php
    use yii\helpers\Json;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use kartik\widgets\Select2;
    use yii\web\JsExpression;
    use kartik\grid\GridView;
    use yii\data\ArrayDataProvider;
    use yii\widgets\Pjax;
    use kartik\daterange\DateRangePicker;
    use common\models\SolicitacaoTransporte;
    use common\models\Escola;
    use common\models\Condutor;
    use common\models\Aluno;
	
	$arrayModalidade = [
        //  0 => 'Nenhum',
        1 => 'FRETE COMUM',
		2 => 'FRETE ADAPTADO',
        3 => 'PASSE ESCOLAR'
    ];
	
	
	
	$anoAtual = date('Y');
	$anoProximo = date('Y')+1;
	$anoAnterior = date('Y')-1;

	$arrayAno = [
        0 => 'Nenhum',
		$anoProximo => $anoProximo,
        $anoAtual => $anoAtual,
		$anoAnterior => $anoAnterior,
    ];

    $this->title = $titulo;
    $arrayAnos = [];
    $this->title = ' Renovações';
    $this->params['breadcrumbs'][] = ['label' => 'Painel de atendimento', 'url' => ['index']];
    $pdfHeader = [
        'L'    => [
            'content' => 'LEFT CONTENT (HEAD)',
        ],
        'C'    => [
            'content'     => 'CENTER CONTENT (HEAD)',
            'font-size'   => 10,
            'font-style'  => 'B',
            'font-family' => 'arial',
            'color'       => '#333333',
        ],
        'R'    => [
            'content' => 'RIGHT CONTENT (HEAD)',
        ],
        'line' => true,
    ];
    $pdfFooter = [
        'L'    => [
            'content'     => '',
            'font-size'   => 10,
            'color'       => '#333333',
            'font-family' => 'arial',
        ],
        'C'    => [
            'content' => '',
        ],
        'R'    => [
            'content'     => '{PAGENO}',
            'font-size'   => 10,
            'color'       => '#333333',
            'font-family' => 'arial',
        ],
        'line' => true,
    ];
	
	
	
    ?>
    <div class="row">
        <div class="col-md-12">
        <div class="box box-solid">

        <div class="box-header with-border">
			<?=
			 Aluno::permissaoRemover() ? Html::a('Encerrar todas as solicitações', ['solicitacao-transporte/encerrar-todas', 'id' => $model->id], [
                'class' => 'btn btn-danger pull-right align-button',
                'data' => [
                    'confirm' => 'Atenção: Ao confirmar o encerramento de todas as solicitações os filtros aplicados nessa página não serão reproduzidos nessa ação. Tem certeza que deseja encerrar todas as solicitações sem renovação? ',
                    'method' => 'post',
                ],
            ]) : ''; 
			?>
           
        </div>
            <div class="box box-solid">
            
                <div class="box-body">
					<h4><span class="label label-primary">Total: <?php echo(count($solicitacoesTransporte));?></span></h4>
					
                    <?= Html::beginForm(['solicitacao-transporte/renovacoes'], 'GET', ['id' => 'formFilter']); ?>
                    <div class="row form-group">
                        <div class="col-md-3">
                        <?php
                            echo Html::label('Tipo de frete', 'tipoFrete');
                                echo Select2::widget([
                                    'name' => 'tipoFrete',
                                'attribute' => 'tipoFrete',
                                'data' => $arrayModalidade,
                                    'value' => $_GET['tipoFrete'] ? $_GET['tipoFrete'] : '',
                                'options' => ['placeholder' => 'Selecione o tipo do frete'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                ]);
                        ?>
                    </div>
                        <div class="col-md-3">
                        <?php
                            echo Html::label('Escola', 'escola');
                                echo Select2::widget([
                                    'name' => 'escola',
                                'attribute' => 'escola',
                                'data' => $escolas,
                                    'value' => $_GET['escola'] ? $_GET['escola'] : '',
                                'options' => ['placeholder' => 'Selecione uma escola'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                ]);
                        ?>
                    </div>
                        <div class="col-md-3">
                        <?php
                            echo Html::label('Unidade', 'unidade');
                                echo Select2::widget([
                                    'name' => 'unidade',
                                'attribute' => 'unidade',
                                'data' => $unidades,
                                    'value' => $_GET['unidade'] ? $_GET['unidade'] : '',
                                'options' => ['placeholder' => 'Selecione a unidade'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                ]);
                        ?>
                    </div>
                        <div class="col-md-3">
                        <?php
                            echo Html::label('Região', 'regiao');
                                echo Select2::widget([
                                    'name' => 'regiao',
                                'attribute' => 'regiao',
                                    'value' => $_GET['regiao'] ? $_GET['regiao'] : '',
                                'data' => $regioes,
                                'options' => ['placeholder' => 'Selecione uma região'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                ]);
                        ?>
                    </div>

                        <div class="col-md-3">
                        <?php
                            echo Html::label('Ano', 'ano');
                                echo Select2::widget([
                                    'name' => 'ano',
                                'attribute' => 'ano',
                                'data' => $arrayAno,
                                    'value' => $_GET['ano'] ? $_GET['ano'] : '',
                                'options' => ['placeholder' => 'Selecione o ano'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                ]);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                            echo Html::label('Tipo', 'tipoSolicitacao');
                                echo Select2::widget([
                                    'name' => 'tipoDeSolicitacao',
                                'attribute' => 'tipoDeSolicitacao',
                                    'value' => $_GET['tipoDeSolicitacao'] ? $_GET['tipoDeSolicitacao'] : '',
                                'data' => SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO,
                                'options' => ['placeholder' => 'Selecione um tipo'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                ]);
                        ?>
                    </div>
					<div class="col-md-3">
                        <?php
                            echo Html::label('Motivo Renovação', 'motivoNaoRenova');
                                echo Select2::widget([
                                'name' => 'motivoNaoRenova',
                                'attribute' => 'motivoNaoRenova',
                                'value' => $_GET['motivoNaoRenova'] ? $_GET['motivoNaoRenova'] : '',
                                'data' => SolicitacaoTransporte::MOTIVO_RENOVACAO,
                                'options' => ['placeholder' => 'Selecione um status'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                ]);
                        ?>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::submitButton('Filtrar', ['class' => 'btn btn-primary pull-right']) ?>
                            
                        </div>
                    </div>

                    <?php echo Html::endForm(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <section class="col-md-12"> 
            <div class="box box-solid">
            <div class="box-body">
                <?php Pjax::begin(); ?>
                <?= GridView::widget([
					'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'panel' => [
                        'heading'=>false,
                        'type'=>false,
                        'showFooter'=>false
                    ],
                    'toolbar' =>  [
                        '{export}{toggleData}',
                    ],
                    'pjax' => true,
                    'pjaxSettings' =>[
                        'neverTimeout'=>true,
                        'options'=>[
                                'id'=>'grid',
                            ]
                        ],

                    'dataProvider' => new ArrayDataProvider([
                    
                    'allModels' => $solicitacoesTransporte,
                    'sort' => [
                        'attributes' => [
                            'id',
                            // 'horarioEntrada',
                            // 'horarioSaida',
                            // 'RA',
                            // 'RAdigito',
                        ],
                    ],
                    'pagination' => [
                        'pageSize' => 100,
                    ],
                    ]),
                    'columns' => [
                        [
                            'class'=>'\kartik\grid\DataColumn',
                            'attribute'=>'id',
                            'filterInputOptions' => ['type' => 'number', 'class' => 'form-control'],
                            'contentOptions' => array('style' => 'min-width:70px;'),
                        ],
						[
                            'attribute' => 'Renovou',
                            'label' => 'Renovação',
                            'value' => function($data) {
								if($data->motivoNaoRenova == 8){
									$status = '-';
								}elseif($data->motivoNaoRenova == 7){
									$status = 'Sim';
								}else{
									$status = 'Não';
								}
								return $status;
                            },
                            //'filter' => SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO
                        ],
						[
                            'attribute' => 'motivoNaoRenova',
                            'label' => 'Motivo Renovação',
                            'value' => function($data) {
								return $data->motivoNaoRenova ? SolicitacaoTransporte::MOTIVO_RENOVACAO[$data->motivoNaoRenova] : '';
                            },
                            //'filter' => SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO
                        ],
						 [
							'class'=>'\kartik\grid\DataColumn',
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
							'attribute' => 'ra',
							'label' => 'RA',
							'value' =>   function($data){
								return  $data->aluno->RA.' '.$data->aluno->RAdigito;
							},
							//'filter' => Aluno::ARRAY_SERIES
						],								
                        [
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
							'attribute' => 'serie',
							'label' => 'Ano/Série e Turma',
							'value' =>   function($data){
								return  Aluno::ARRAY_SERIES[$data->aluno->serie].'-'.Aluno::ARRAY_TURMA[$data->aluno-turma];
							},
							//'filter' => Aluno::ARRAY_SERIES
						],			
						[
							'attribute' => 'endereco_alterado',
							'label' => 'Endereço alterado?',
							'value' =>   function($data){
								if($data->aluno->atualiza_endereco_renovacao == 0){
									$status = 'Não';
								}else{
									$status = 'Sim';
								}
								return $status;
							},
							//'filter' => Aluno::ARRAY_SERIES
						],								
						[
							'attribute' => 'Endereco',
							'label' => 'Endereço',
							'value' =>   function($data){
								return  $data->aluno->tipoLogradouro.' '.$data->aluno->endereco.', '.$data->aluno->numeroResidencia.', '.$data->aluno->cep.', '.$data->aluno->bairro;
							},
							//'filter' => Aluno::ARRAY_SERIES
						],	
						[
                            'attribute' => 'novaSolicitacao',
                            'label' => 'Tipo da solicitação',
                            'value' => function($data) {
                                return $data->novaSolicitacao ? SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO[$data->novaSolicitacao] : '-';
                            },
                            'filter' => SolicitacaoTransporte::ARRAY_NOVA_SOLICITACAO
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

                        
                            'contentOptions' => array('style' => 'width:150px;'),
                        ],
                        [
                            'attribute' => 'anoVigente',
                            'label' => "Ano da solicitação"
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
                            'label' => 'Necessidades especiais',
                            'attribute' => 'necessidadeEspecial',
                            'filter' =>  [1 => 'Não', 2 => 'Sim'],
                            'value' => function ($model) {
                                $listaNecessidade = [];
                                foreach ($model->aluno->necessidades as $tipoNecessidade)
                                {
                                    $listaNecessidade[] = $tipoNecessidade->necessidadesEspeciais->nome;
                                }

                                return implode (',', $listaNecessidade);
                            },
                        ],
                       
                       
                     
                    ],
                    'exportConfig' => [
                        GridView::HTML => true,
                        GridView::CSV => true,
                        GridView::TEXT => true,
                        GridView::EXCEL => true,
                        GridView::PDF => [
                            'config' => [
                                'mode' => 'c',
                                'format' => 'A4-L',
                                'destination' => 'D',
                                'marginTop' => 20,
                                'marginBottom' => 20,
                                'marginLeft' => 5,
                                'marginRight' => 5,
                                'cssInline' => 
                                    '.table{font-size:10px}' .
                                    '.kv-wrap{padding:20px;}' .
                                    '.kv-align-center{text-align:center;}' .
                                    '.kv-align-left{text-align:left;}' .
                                    '.kv-align-right{text-align:right;}' .
                                    '.kv-align-top{vertical-align:top!important;}' .
                                    '.kv-align-bottom{vertical-align:bottom!important;}' .
                                    '.kv-align-middle{vertical-align:middle!important;}' .
                                    '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                                    '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                                    '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                                'methods' => [
                                    'SetHeader' => [
                                        ['odd' => '', 'even' => '']
                                    ],
                                    'SetFooter' => [
                                        ['odd' => $pdfFooter, 'even' => $pdfFooter]
                                    ],
                                ],
                                'options' => [
                                    'title' => $title,
                                    'subject' => 'xx1',
                                    'keywords' => 'xx3',
                                ],
                                'contentBefore'=>'',
                                'contentAfter'=>''
                            ]
                        ],
                    ],
             
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
            </div>
        </section>
    </div>


