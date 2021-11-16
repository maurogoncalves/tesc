<?php

namespace frontend\controllers;

use Yii;
use common\models\Usuario;
use common\models\Aluno;
use common\models\AlunoNecessidadesEspeciais;
use common\models\Escola;
use common\models\Historico;
use common\models\HistoricoAluno;
use common\models\SolicitacaoTransporte;
use common\models\SolicitacaoCredito;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;
use yii\web\UploadedFile;
use common\components\AccessRule;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;

/**
 * CfopController implements the CRUD actions for Cfop model.
 */
class RelatorioController extends Controller
{
    const ANUAL_BAR_CHART = [
            'chart' => [
                'type' => 'column',
            ],
            'title' => [
                'text' => ''
            ],
            'yAxis' => [
                'title' => [
                    'text' => ''
                ]
            ],
            'xAxis' => [
                'categories' => [
                    'Jan',
                    'Fev',
                    'Mar',
                    'Abr',
                    'Mai',
                    'Jun',
                    'Jul',
                    'Ago',
                    'Set',
                    'Out',
                    'Nov',
                    'Dez'
                ],
            ],
            'series' => []
        ];

    const ANUAL_LINE_CHART = [
            'chart' => [
                'type' => 'line',
            ],
            'title' => [
                'text' => ''
            ],
            'yAxis' => [
                'title' => [
                    'text' => ''
                ]
            ],
            'xAxis' => [
                'categories' => [
                    'Jan',
                    'Fev',
                    'Mar',
                    'Abr',
                    'Mai',
                    'Jun',
                    'Jul',
                    'Ago',
                    'Set',
                    'Out',
                    'Nov',
                    'Dez'
                ],
            ],
            'series' => []
        ];

    const ANUAL_SPLINE_CHART = [
            'chart' => [
                'type' => 'spline',
            ],
            'title' => [
                'text' => ''
            ],
            'yAxis' => [
                'title' => [
                    'text' => ''
                ]
            ],
            'xAxis' => [
                'categories' => [
                    'Jan',
                    'Fev',
                    'Mar',
                    'Abr',
                    'Mai',
                    'Jun',
                    'Jul',
                    'Ago',
                    'Set',
                    'Out',
                    'Nov',
                    'Dez'
                ],
            ],
            'series' => []
        ];

    const ANUAL_AREASPLINE_CHART = [
            'chart' => [
                'type' => 'areaspline',
            ],
            // 'rangeSelector' => [
            //     'enabled' => true,
            //     'selected' => 1
            // ],
            'title' => [
                'text' => ''
            ],
            'plotOptions' => [
                'areaspline' => [
                    'fillOpacity' => 0.5
                ]
            ],
            'yAxis' => [
                'title' => [
                    'text' => ''
                ]
            ],
            'xAxis' => [
                // 'type' => 'month',
                'categories' => [
                    'Jan',
                    'Fev',
                    'Mar',
                    'Abr',
                    'Mai',
                    'Jun',
                    'Jul',
                    'Ago',
                    'Set',
                    'Out',
                    'Nov',
                    'Dez'
                ],
            ],
            'series' => []
        ];

    const PIE_CHART = [
            'chart' => [
                'type' => 'pie'
            ],
            'title' => [
                'text' => ''
            ],
            'tooltip' => [
                'pointFormat' => ' <b>{point.percentage:.1f}%</b>'
            ],
            'plotOptions' => [
                'pie' => [
                    'allowPointSelect' => true,
                    'cursor' => 'pointer',
                    'dataLabels' => [
                        'enabled' => true,
                        'format' => '<b>{point.name}:{point.y}</b>',
                        'style' => [
                            // 'color' => (Highcharts::theme && Highcharts.theme.contrastTextColor) || 'black'
                        ]
                    ],
                    'showInLegend' => true,
                    'innerSize' => '50%'
                ]
            ],
            'yAxis' => [
                'title' => [
                    'text' => ''
                ]
            ],
            'series' => [
                [
                    'name' => '',
                    'colorByPoint' => true,
                    'data' => [],
                    'format' => '<b>{point.name}</b>:{point.y}',
                    
                ]
            ]
        ];


        // dataLabels: {
        //     formatter: function () {
        //         return this.y > 5 ? this.point.name : null;
        //     },
        //     color: '#ffffff',
        //     distance: -30
        // }

    // By Samuel
    const ARRAY_VALORES = [0,0,0,0,0,0,0,0,0,0,0,0];
    const ARRAY_MESES = ['','Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
    
    public function init()
    {
        parent::init();
        // FORÇAR DESABILITAR WARNINGS (E_NOTICE) E MOSTRAR
        // SÓ O QUE FOR ERROR
        ini_set('error_reporting', E_ERROR);

    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['alunos-modalidade', 'alunos-tipo-transporte', 'alunos-tipo-rede', 'alunos-pne', 'espera-modalidade', 'espera-tipo-transporte', 'espera-tipo-rede', 'espera-pne'],
                'rules' => [
                    [
                        'actions' => ['alunos-modalidade', 'alunos-tipo-transporte', 'alunos-tipo-rede', 'alunos-pne', 'espera-modalidade', 'espera-tipo-transporte', 'espera-tipo-rede', 'espera-pne'],
                        'allow' => true,
                        // 'roles' => [
                        //     Usuario::PERFIL_SUPER_ADMIN,
                        // ],
                    ],
                ],
            ]
        ];
    }

    private function alunosSemTransporte(){
        $resultado = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade')
                ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                ->andWhere(['=', 'SolicitacaoTransporte.modalidadeBeneficio', Aluno::MODALIDADE_FRETE])
                ->asArray()
                ->one();
        if($resultado){
            return $resultado['quantidade']?$resultado['quantidade']:0;   
        }
        return 0;
        
    }

    private function alunosTransportadosHoje(){
        $resultado =HistoricoAluno::find()
                 ->select('COUNT(*) AS quantidade')
                 // ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
                ->joinWith('historico')
                ->andWhere(['data' => date('Y-m-d')])
                ->asArray()
                ->one();
        if($resultado){
            return $resultado['quantidade']?$resultado['quantidade']:0;   
        }
        return 0;
        
    }   

        private function alunosKmRodado(){
        $resultado =Historico::find()
                 ->select(' sum(distanciaTotal) AS quantidade')
                 // ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
             
                ->andWhere(['data' => date('Y-m-d')])
                ->asArray()
                ->one();
        if($resultado){
            return $resultado['quantidade']?$resultado['quantidade']:0;
        }
        return 0;
        
    }
    // public function actionTeste(){ 
    //             ini_set('error_reporting', E_ALL);

    //     print_r( $this->alunosKmRodado() );
    // }
    public function actionDashboard(){

      return $this->render('dashboard', [
            //Ativos
            'alunosModalidade' => $this->alunosModalidade(),
            'alunoTipoTransporte' => $this->alunoTipoTransporte(),
            'alunosTipoRede' => $this->alunosTipoRede(),
            'alunosPne' => $this->alunosPne(),

            //Espera
            'alunosModalidadeEspera' => $this->alunosModalidadeEspera(),
            'esperaTipoTransporte' => $this->esperaTipoTransporte(),
            'esperaTipoRede' => $this->esperaTipoRede(),
            'esperaPne' => $this->esperaPne(),

            //Contagem
            'alunosSemTransporte' => $this->alunosSemTransporte(),
            'alunosTransportadosHoje' => $this->alunosTransportadosHoje(),
            'alunosKmRodado' => $this->alunosKmRodado(),

        ]);
    }
    //here
    private function alunosModalidade(){
        $arrayData = Self::PIE_CHART;  
        $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        $alunosModalidade = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade, SolicitacaoTransporte.modalidadeBeneficio AS modalidadeBeneficio')
                ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
                ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
                ->andWhere(['modalidadeBeneficio' => Aluno::MODALIDADE_FRETE])

                ->groupBy('SolicitacaoTransporte.modalidadeBeneficio')
                ->asArray()
                ->all();

            $alunosTipoFrete = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade, SolicitacaoTransporte.tipoFrete AS tipoFrete')
                ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
                ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
                ->andWhere(['modalidadeBeneficio' => Aluno::MODALIDADE_FRETE])
                ->groupBy('SolicitacaoTransporte.tipoFrete')
                ->asArray()
                ->all();

           
            $totalPasse = SolicitacaoTransporte::find()
            ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
            ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
            ->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE])
            ->all();

        foreach ($alunosTipoFrete as $tipoFrete) {
            if($tipoFrete['tipoFrete'] == SolicitacaoTransporte::TIPO_FRETE_COMUM)
                $alunosTipoFreteComum = $tipoFrete['quantidade']; 
            if($tipoFrete['tipoFrete'] == SolicitacaoTransporte::TIPO_FRETE_ADAPTADO)
                $alunosTipoFreteAdaptado = $tipoFrete['quantidade']; 
        }

        // print_r($alunosTipoFrete); 
        // print_r($alunosTipoFreteComum);
        foreach ($alunosModalidade as $item)
        {
            $arrayData['series'][0]['name'] = 'Modalidades';
            $arrayData['series'][0]['data'][] = [
                'name' => Aluno::ARRAY_MODALIDADE[$item['modalidadeBeneficio']],
                'y' => floatval($item['quantidade']),
                'drilldown' => $item['modalidadeBeneficio'] == Aluno::MODALIDADE_FRETE ? 'Tipo de frete ativo' : false
            ];
            if($item['modalidadeBeneficio'] == Aluno::MODALIDADE_FRETE){
                $arrayData['drilldown']['series'][0]['name'] = 'Tipo de frete';
                $arrayData['drilldown']['series'][0]['id'] = 'Tipo de frete ativo';
                $arrayData['drilldown']['series'][0]['data'][] = [
                    'name' => 'Adaptado',
                    'y' => floatval($alunosTipoFreteAdaptado)
                ]; 
                  $arrayData['drilldown']['series'][0]['data'][] = [
                    'name' =>'Comum',
                    'y' => floatval($alunosTipoFreteComum)
                ]; 
            }
            $arrayData['series'][0]['data'][] = [
                'name' => 'PASSE ESCOLAR',
                'y' => count($totalPasse),
                'drilldown' => false
            ];
            
               
        }
        return $arrayData;
    }
    public function actionAlunosModalidade()
    {
        $arrayData = $this->alunosModalidade();
        return $this->render('alunos-modalidade', [
            'arrayData' => $arrayData
        ]);
    }
    private function alunoTipoTransporte(){
        $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        $arrayData['series'][0]['name'] = 'Tipo de transporte';

        $arrayPNE = AlunoNecessidadesEspeciais::find()->select('idAluno')->asArray()->all();
        $arrayPNE = array_column($arrayPNE, 'idAluno');

        $transporteRegular = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade')
                ->where(['not in', 'SolicitacaoTransporte.idAluno', $arrayPNE])
                ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
                // ->andWhere(['=', 'SolicitacaoTransporte.data', 'maxData'])
                ->asArray()
                ->one();

        $transportePNE = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade')
                ->where(['SolicitacaoTransporte.idAluno'=>$arrayPNE])
                ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
                // ->andWhere(['=', 'SolicitacaoTransporte.data', 'maxData'])
                ->asArray()
                ->one();
// throw new NotFoundHttpException(print_r($transportePNE, true));
        $arrayData['series'][0]['data'][] = [
            'name' => 'Regular',
            'y' => floatval($transporteRegular['quantidade'])
        ];
        $arrayData['series'][0]['data'][] = [
            'name' => 'PNE',
            'y' => floatval($transportePNE['quantidade'])
        ];
        return $arrayData;
    }   
    public function actionAlunosTipoTransporte()
    {
        $arrayData = $this->alunoTipoTransporte();
        return $this->render('alunos-tipo-transporte', [
            'arrayData' => $arrayData
        ]);
    }

    private function alunosTipoRede(){
          $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        $alunosModalidade = Aluno::find()
                ->select('COUNT(Aluno.id) AS quantidade, Escola.unidade AS unidade')
                ->joinWith('escola')
                ->groupBy('Escola.unidade')
                ->asArray()
                ->all();
        $escolaTipos = Aluno::find()
                ->select('COUNT(Aluno.id) AS quantidade, Escola.tipo AS tipo')
                ->joinWith('escola')
                ->andWhere(['Escola.unidade' => Escola::UNIDADE_MUNICIPAL])
                ->groupBy('Escola.tipo')
                ->asArray()
                ->all();
        foreach ($alunosModalidade as $item)
        {
            $arrayData['series'][0]['name'] = 'Tipo de rede';
            $arrayData['series'][0]['data'][] = [
                'name' => Escola::ARRAY_UNIDADE[$item['unidade']],
                'y' => floatval($item['quantidade']),
                'drilldown' => $item['unidade'] == Escola::UNIDADE_MUNICIPAL ? 'Tipo de escola' : false
            ];
            if($item['unidade'] == Escola::UNIDADE_MUNICIPAL){
                $arrayData['drilldown']['series'][0]['name'] = 'Tipo de escola';
                $arrayData['drilldown']['series'][0]['id'] = 'Tipo de escola';
                
                foreach ($escolaTipos as $escolaTipo) {
                     $arrayData['drilldown']['series'][0]['data'][] = [
                        'name' => Escola::ARRAY_TIPO[$escolaTipo['tipo']],
                        'y' => floatval($escolaTipo['quantidade'])
                    ];
                 } 
               
            }
        }

        return $arrayData ;
    }
    public function actionAlunosTipoRede()
    {
        $arrayData = $this->alunosTipoRede();

        return $this->render('alunos-tipo-rede', [
            'arrayData' => $arrayData
        ]);
    }

    private function alunosPne(){
        $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        $preAlunosModalidade = AlunoNecessidadesEspeciais::find()
                ->select('Escola.unidade AS unidade')
                ->joinWith('solicitacoes')
                ->joinWith('escolas')
                ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
                ->groupBy('Escola.id')
                ->asArray()
                ->all();

        foreach ($preAlunosModalidade as $pre) {
            if(!isset($preAlunosModalidade[$pre['unidade']]) ){
                $alunosModalidade[$pre['unidade']] = [];
                $alunosModalidade[$pre['unidade']]['quantidade'] = 0;
            }
            $alunosModalidade[$pre['unidade']]['unidade'] = $pre['unidade'];
            $alunosModalidade[$pre['unidade']]['quantidade'] += 1;
        }

        foreach ($alunosModalidade as $item)
        {
            $arrayData['series'][0]['name'] = 'PNE';
            $arrayData['series'][0]['data'][] = [
                'name' => Escola::ARRAY_UNIDADE[$item['unidade']],
                'y' => floatval($item['quantidade'])
            ];
        }
        return $arrayData;
    }
    public function actionAlunosPne()
    {
        
        $arrayData = $this->alunosPne();
        return $this->render('alunos-pne', [
            'arrayData' => $arrayData
        ]);
    }

    private function alunosModalidadeEspera(){
        $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        $alunosModalidadeFrete = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade, SolicitacaoTransporte.modalidadeBeneficio AS modalidadeBeneficio')
                ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
                ->andWhere(['modalidadeBeneficio' => Aluno::MODALIDADE_FRETE])
                ->groupBy('SolicitacaoTransporte.modalidadeBeneficio')
                ->asArray()
                ->one();

        $alunosTipoFrete = SolicitacaoTransporte::find()
            ->select('COUNT(SolicitacaoTransporte.id) AS quantidade, SolicitacaoTransporte.tipoFrete AS tipoFrete')
            ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
            ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
            ->andWhere(['modalidadeBeneficio' => Aluno::MODALIDADE_FRETE])
            ->groupBy('SolicitacaoTransporte.tipoFrete')
            ->asArray()
            ->one();

            $alunosModalidadePasseMun = SolicitacaoTransporte::find()
            ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR])
            ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
            ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
            ->andWhere(['Escola.unidade' => Escola::UNIDADE_MUNICIPAL])
            ->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE])
            // ->asArray()
            ->all();

            $alunosModalidadePasseDre = SolicitacaoTransporte::find()
            ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO_DRE])
            ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
            ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
            ->andWhere(['Escola.unidade' => Escola::UNIDADE_ESTADUAL])
            ->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE])
            // ->asArray()
            ->all();

            $quantidade = 0;
            foreach($alunosModalidadePasseDre as $solDre) {
                $quantidade++;
            }
            foreach($alunosModalidadePasseMun as $solMun) {
                $quantidade++;
            }
            // print_r($alunosModalidadePasseDre);
            // print_r($alunosModalidadePasseMun);
            // exit(1);
            $alunosModalidadePasse = [
                'quantidade' => $quantidade,
                'modalidadeBeneficio' =>  'PASSE ESCOLAR' 
            ];
            // print_r($alunosModalidadePasse);
    
        foreach ($alunosTipoFrete as $tipoFrete) {
            if($tipoFrete['tipoFrete'] == SolicitacaoTransporte::TIPO_FRETE_COMUM)
                $alunosTipoFreteComum = $tipoFrete['quantidade']; 
            if($tipoFrete['tipoFrete'] == SolicitacaoTransporte::TIPO_FRETE_ADAPTADO)
                $alunosTipoFreteAdaptado = $tipoFrete['quantidade']; 
        }

        // print_r($alunosTipoFrete); 
        // print_r($alunosTipoFreteComum);
        
            $arrayData['series'][0]['name'] = 'Lista de espera por modalidade';
            $arrayData['series'][0]['data'][] = [
                'name' => Aluno::ARRAY_MODALIDADE[$alunosModalidadeFrete['modalidadeBeneficio']],
                'y' => floatval($alunosModalidadeFrete['quantidade']),
                'drilldown' => $alunosModalidadeFrete['modalidadeBeneficio'] == Aluno::MODALIDADE_FRETE ? 'Tipo de frete' : false
            ];
        
            $arrayData['drilldown']['series'][0]['name'] = 'Tipo de frete';
            $arrayData['drilldown']['series'][0]['id'] = 'Tipo de frete';
            $arrayData['drilldown']['series'][0]['data'][] = [
                'name' => 'Adaptado',
                'y' => floatval($alunosTipoFreteAdaptado)
            ]; 
                $arrayData['drilldown']['series'][0]['data'][] = [
                'name' =>'Comum',
                'y' => floatval($alunosTipoFreteComum)
            ]; 
            
            $arrayData['series'][0]['data'][] = [
                'name' => 'PASSE ESCOLAR',
                'y' => floatval($alunosModalidadePasse['quantidade']),
                'drilldown' => false
            ];

        return $arrayData;
    }
    public function actionEsperaModalidade()
    { 
        $arrayData = $this->alunosModalidadeEspera();
        return $this->render('espera-modalidade', [
            'arrayData' => $arrayData
        ]);
    }

    private function esperaTipoTransporte(){
        $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        $arrayData['series'][0]['name'] = 'Tipo de transporte';

        $arrayPNE = AlunoNecessidadesEspeciais::find()->asArray()->all();

        $arrayPNE = array_column($arrayPNE, 'idAluno');

        $transporteRegular = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade')
                ->where(['not in', 'SolicitacaoTransporte.idAluno', $arrayPNE])
                ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                // ->andWhere(['=', 'SolicitacaoTransporte.data', 'maxData'])
                ->asArray()
                ->one();

        $transportePNE = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade')
                ->where(['SolicitacaoTransporte.idAluno'=>$arrayPNE])
                ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                // ->andWhere(['=', 'SolicitacaoTransporte.data', 'maxData'])
                ->asArray()
                ->one();

        $arrayData['series'][0]['data'][] = [
            'name' => 'Regular',
            'y' => floatval($transporteRegular['quantidade'])
        ];
        $arrayData['series'][0]['data'][] = [
            'name' => 'PNE',
            'y' => floatval($transportePNE['quantidade'])
        ];

        return $arrayData;
    }
    public function actionEsperaTipoTransporte()
    {
        $arrayData = $this->esperaTipoTransporte();

        return $this->render('espera-tipo-transporte', [
            'arrayData' => $arrayData
        ]);
    }
    private function esperaTipoRede(){
           $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        $alunosModalidade = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade, Escola.unidade AS unidade')
                ->joinWith('escola')
                ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                ->groupBy('Escola.unidade')
                ->asArray()
                ->all();

        // foreach ($alunosModalidade as $item)
        // {
        //     $arrayData['series'][0]['name'] = 'Lista de espera por tipo de transporte';
        //     $arrayData['series'][0]['data'][] = [
        //         'name' => Escola::ARRAY_UNIDADE[$item['unidade']],
        //         'y' => floatval($item['quantidade'])
        //     ];
        // }
         $escolaTipos = Aluno::find()
                ->select('COUNT(Aluno.id) AS quantidade, Escola.tipo AS tipo')
                ->joinWith('escola')
                ->andWhere(['Escola.unidade' => Escola::UNIDADE_MUNICIPAL])
                ->groupBy('Escola.tipo')
                ->asArray()
                ->all();

         $escolaTipos = SolicitacaoTransporte::find()
                ->select('COUNT(SolicitacaoTransporte.id) AS quantidade, Escola.tipo AS tipo')
                ->joinWith('escola')
                ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                ->andWhere(['Escola.unidade' => Escola::UNIDADE_MUNICIPAL])
                ->groupBy('Escola.tipo')
                ->asArray()
                ->all();
        foreach ($alunosModalidade as $item)
        {
            $arrayData['series'][0]['name'] = 'Lista de espera por tipo de rede';
            $arrayData['series'][0]['data'][] = [
                'name' => Escola::ARRAY_UNIDADE[$item['unidade']],
                'y' => floatval($item['quantidade']),
                'drilldown' => $item['unidade'] == Escola::UNIDADE_MUNICIPAL ? 'Tipo de escola' : false
            ];
            if($item['unidade'] == Escola::UNIDADE_MUNICIPAL){
                $arrayData['drilldown']['series'][0]['name'] = 'Tipo de escola';
                $arrayData['drilldown']['series'][0]['id'] = 'Tipo de escola';
                
                foreach ($escolaTipos as $escolaTipo) {
                     $arrayData['drilldown']['series'][0]['data'][] = [
                        'name' => Escola::ARRAY_TIPO[$escolaTipo['tipo']],
                        'y' => floatval($escolaTipo['quantidade'])
                    ];
                 } 
               
            }
        }
        return $arrayData;
    }
    public function actionEsperaTipoRede()
    {
        $arrayData = $this->esperaTipoRede();

        return $this->render('espera-tipo-rede', [
            'arrayData' => $arrayData
        ]);
    }

    private function esperaPne(){
           $get = Yii::$app->request->get();
        // Aqui faremos a query
        $data = [];

        $arrayData = Self::PIE_CHART;
        // $alunosModalidade = AlunoNecessidadesEspeciais::find()
        //         ->select('COUNT(AlunoNecessidadesEspeciais.id) AS quantidade, NecessidadesEspeciais.nome AS necessidade')
        //         ->joinWith('solicitacoes')
        //         ->joinWith('necessidadesEspeciais')
        //         ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
        //         ->groupBy('AlunoNecessidadesEspeciais.idNecessidadesEspeciais')
        //         ->asArray()
        //         ->all();

        // foreach ($alunosModalidade as $item)
        // {
        //     $arrayData['series'][0]['name'] = 'PNE';
        //     $arrayData['series'][0]['data'][] = [
        //         'name' => $item['necessidade'],
        //         'y' => floatval($item['quantidade'])
        //     ];
        // }
        $preAlunosModalidade = AlunoNecessidadesEspeciais::find()
                ->select('Escola.unidade AS unidade')
                ->joinWith('solicitacoes')
                ->joinWith('escolas')
                ->where(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                ->groupBy('Escola.id')
                ->asArray()
                ->all();

        foreach ($preAlunosModalidade as $pre) {
            if(!isset($preAlunosModalidade[$pre['unidade']]) ){
                $alunosModalidade[$pre['unidade']] = [];
                $alunosModalidade[$pre['unidade']]['quantidade'] = 0;
            }
            $alunosModalidade[$pre['unidade']]['unidade'] = $pre['unidade'];
            $alunosModalidade[$pre['unidade']]['quantidade'] += 1;
        }

        foreach ($alunosModalidade as $item)
        {
            $arrayData['series'][0]['name'] = 'PNE';
            $arrayData['series'][0]['data'][] = [
                'name' => Escola::ARRAY_UNIDADE[$item['unidade']],
                'y' => floatval($item['quantidade'])
            ];
        }

        return $arrayData;
    }
    public function actionEsperaPne()
    {
     
        $arrayData = $this->esperaPne();
        return $this->render('espera-pne', [
            'arrayData' => $arrayData
        ]);
    }

    public function actionPasseEscola()
    {
        $arrayData = [];
        $get = Yii::$app->request->get();

        if ($get['periodo'])
        {
            $datas = explode(' - ', $get['periodo']);
            $dtInicial = \DateTime::createFromFormat ( 'd/m/Y', $datas[0]);
            $dtFinal = \DateTime::createFromFormat ( 'd/m/Y', $datas[1]);
            // throw new NotFoundHttpException(print_r($datas, true));

            if (count($datas) != 2)
                \Yii::$app->getSession()->setFlash('error', 'Selecione um período válido para a consulta.');

            $result = SolicitacaoCredito::find()
                ->select('SUM(SolicitacaoCreditoAluno.valor) AS valor, Escola.nome AS nomeEscola, Escola.id AS idEscola')
                ->joinWith('escola')
                ->joinWith('solicitacaoCreditoAlunos')
                ->where(['=', 'SolicitacaoCredito.status', SolicitacaoCredito::STATUS_EFETIVADA])
                ->groupBy('Escola.id')
                ->orderBy('Escola.nome DESC');

            if ($dtFinal)
                $result->andFilterWhere(['>=', 'SolicitacaoCredito.criado', $dtInicial->format('Y-m-d')]);
            
            if ($dtFinal)
                $result->andFilterWhere(['<=', 'SolicitacaoCredito.criado', $dtFinal->format('Y-m-d')]);

            $arrayData = $result->all();
        }

        return $this->render('passe-escola', [
            'arrayData' => $arrayData
        ]);
    }

    public function actionAlunosTransportados()
    {
        $arrayData = [];
        $titulo = 'Relação de alunos transportados';
       
        // Quando o pdf for solicitado via post, faremos dessa forma
        $post = Yii::$app->request->post();

        if (isset($post['pdf']))
        {
            // throw new NotFoundHttpException(print_r($post['content'], true));
            $content = '<body>';
            $content .= $post['content'];
            $content .= '</body>';
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
                'content' => $content,
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.css',
                // 'cssFile' => '@vendor/almasaeed2010/adminlte/dist/css/AdminLTE.css',
                'cssInline' => 'td{font-size: 11px;}th{font-size: 11px!important;} h5{font-size:14px!important;}h4{font-size: 18px!important;}',
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_STRING,
                'options' => [
                    'title' => $titulo,
                    'subject' => ''
                ],
                'methods' => [
                    
                    'SetFooter' => ['|Página {PAGENO}|'],
                ]
            ]);
            return base64_encode($pdf->render());
        }

        $get = Yii::$app->request->get();
        if (!isset($get['periodo']))
        {
            // \Yii::$app->getSession()->setFlash('error', 'Selecione um período para a consulta.');
        }
        else
        {
            $datas = explode(' - ', $get['periodo']);
            $dtInicial = \DateTime::createFromFormat ( 'd/m/Y', $datas[0]);
            $dtFinal = \DateTime::createFromFormat ( 'd/m/Y', $datas[1]);
            // throw new NotFoundHttpException(print_r($datas, true));

            $result = HistoricoAluno::find();
            $result->joinWith('escola');
            $result->joinWith('historico');

            if (count($datas) != 2)
                \Yii::$app->getSession()->setFlash('error', 'Selecione um período válido para a consulta.');

            if ($dtFinal)
                $result->andFilterWhere(['>=', 'Historico.data', $dtInicial->format('Y-m-d')]);
            
            if ($dtFinal)
                $result->andFilterWhere(['<=', 'Historico.data', $dtFinal->format('Y-m-d')]);

            if (isset($get['condutor']))
                $result->andFilterWhere(['=', 'Historico.idCondutor', $get['condutor']]);

            if (isset($get['escola']))
                $result->andFilterWhere(['=', 'Escola.id', $get['escola']]);

            $arrayData = $result->all();

            if (!$arrayData)
                \Yii::$app->getSession()->setFlash('error', 'Nenhum resultado encontrado.');

            if (isset($get['pdf']))
            {
                $this->layout = 'main-login';
                $content = $this->render('alunos-transportados', [
                    'data' => $arrayData,
                    'titulo' => $titulo,
                    'get' => $get
                ]);

                
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
                    'content' => $content,
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.css',
                    // 'cssInline' => '.kv-heading-1{font-size:18px}',
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_LANDSCAPE,
                    'destination' => Pdf::DEST_BROWSER,
                    'options' => [
                        'title' => $titulo,
                        'subject' => ''
                    ],
                    'methods' => [
                        
                        'SetFooter' => ['|Página {PAGENO}|'],
                    ]
                ]);
                return $pdf->render();
            }
            else
                return $this->render('alunos-transportados', [
                    'data' => $arrayData,
                    'titulo' => $titulo,
                    'get' => $get
                ]);
        }
        
        return $this->render('alunos-transportados', [
            'data' => $arrayData,
            'titulo' => $titulo,
            'get' => $get
        ]);
    }

    public function actionPainelIndicadores()
    {
        return $this->render('painel-indicadores', [

        ]);
    }
}
//cd D:\transporte-escolar