<?php

namespace frontend\controllers;

use Yii;
use common\models\SolicitacaoTransporte;
use yii\helpers\ArrayHelper;
use common\models\SolicitacaoTransporteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use common\models\TipoDocumento;
use common\models\DocumentoAluno;
use yii\web\UploadedFile;
use common\models\Usuario;
use common\models\Escola;
use common\models\UsuarioGrupo;
use common\models\SolicitacaoStatus;
use common\models\Aluno;
use common\models\PontoAluno;
use common\models\EscolaDiretor;
use common\models\EscolaSecretario;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\services\DirectionsWayPoint;
use dosamigos\google\maps\services\TravelMode;
use dosamigos\google\maps\overlays\PolylineOptions;
use dosamigos\google\maps\services\DirectionsRenderer;
use dosamigos\google\maps\services\DirectionsService;
use dosamigos\google\maps\overlays\InfoWindow;
use dosamigos\google\maps\overlays\Marker;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\services\DirectionsRequest;
use dosamigos\google\maps\overlays\Polygon;
use dosamigos\google\maps\layers\BicyclingLayer;
use dosamigos\google\maps\services\DirectionsClient;
use yii\filters\AccessControl;
use common\models\DocumentoSolicitacao;
use common\models\SolicitacaoTransporteEscolas;
use common\models\Configuracao;
use common\models\HistoricoMovimentacaoRota;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
//use cirovargas\GoogleDistanceMatrix;

/**
 * SolicitacaoTransporteController implements the CRUD actions for SolicitacaoTransporte model.
 */
class SolicitacaoTransporteController extends Controller
{
    protected $configuracao;

    public function init()
    {
        parent::init();
        $this->configuracao = Configuracao::setup();
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // ...
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionTeste()
    {
        // $distanceMatrix = new GoogleDistanceMatrix('AIzaSyCLdXxxtVSN5I0NA2WJ2buip_pEwfF2pW0');
        // $distance = $distanceMatrix->setLanguage('ptbr')
        //     ->addOrigin('49.950096, 14.668544')
        //     ->addOrigin('49.950096, 15.668544')
        //     ->addDestination('50.031817, 14.490880')
        //     ->addDestination('51.031817, 14.490880')
        //     ->sendRequest();

    }
    /**
     * Lists all SolicitacaoTransporte models.
     * @return mixed
     */
    public function actionIndex()
    {

        $solicitacoesPermitidas = [];
        $solicitacoes = SolicitacaoTransporte::find()->all();

        //Antigo forçar mostrar só as do grupo
        //$solicitacoesPermitidas = UsuarioGrupo::solicitacoesPermitidas($solicitacoes);
        foreach ($solicitacoes as $solicitacao) {
            $solicitacoesPermitidas[] = $solicitacao->id;
        }

        $searchModel = new SolicitacaoTransporteSearch();

        $searchModel->anoVigente = $this->configuracao->calcularAno();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $solicitacoesPermitidas);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        //go horse para forçar não printar nada quando não tem permissão 
        // arrumar em breve
        // Quem escreveu o código abaixo foi o Elton (elton@devell.com.br)
        if (!Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::TESC_CONSULTA, Usuario::PERFIL_DRE, Usuario::PERFIL_DIRETOR, Usuario::PERFIL_SECRETARIO, Usuario::PERFIL_TESC_PASSE_ESCOLAR]) && !$solicitacoesPermitidas) {
            $dataProvider->query->andFilterWhere(['Escola.id' => 99999]);
        }

        if (Usuario::permissao(Usuario::PERFIL_TESC_PASSE_ESCOLAR)) {
            $dataProvider->query->andFilterWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE]);
        }

        // if(Usuario::permissao(Usuario::PERFIL_DIRETOR) )
        //   $dataProvider->query->andFilterWhere(['idEscola' => 1739]);

        if (Usuario::permissao(Usuario::PERFIL_DRE))
            $dataProvider->query->andFilterWhere(['Escola.unidade' => Escola::UNIDADE_ESTADUAL]);

        if (Usuario::permissao(Usuario::PERFIL_DIRETOR)) {
            $ids = EscolaDiretor::listaEscolas();
            $ids[] = 999999;
            $dataProvider->query->andFilterWhere(['in', 'Escola.id', $ids]);
        }

        if (Usuario::permissao(Usuario::PERFIL_SECRETARIO)) {
            $ids = EscolaSecretario::listaEscolas();
            $ids[] = 999999;
            $dataProvider->query->andFilterWhere(['in', 'Escola.id', $ids]);
        }

        $dataProvider->pagination = ['pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20];
        // cast list object in dataprovider
        // $dataProvider->query->join('aluno')->andFilterWhere(['status'=>1]); 
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SolicitacaoTransporte model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $solicitacao = $this->findModel($id);
        return $this->render('view', [
            'model' => $solicitacao,
            'status' => SolicitacaoStatus::find()->where(['idSolicitacaoTransporte' => $id])->all(),
        ]);
    }

    public function actionViewAjax($id)
    {
        return $this->renderAjax('viewAjax', [
            'model' => $this->findModel($id),

        ]);
        // return $this->render('view', [
        //     'model' => $this->findModel($id),
        // ]);
    }

    public function actionAlteracaoStatusAjaxAdmin($status, $alterarDadosProtegidos = false)
    {
        $model = new SolicitacaoStatus();
        $model->mostrar = 1;

        $id =  Yii::$app->request->get('id');
        $status =  Yii::$app->request->get('status');
        if ($id) {
            $model->idSolicitacaoTransporte = $id;
            $solicitacao = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->idUsuario = \Yii::$app->User->identity->id;
            $model->dataCadastro = date('Y-m-d');
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (!$model->save()) {
                return ['status' => false, 'errors' => $model->getErrors()];
            }
            $solicitacao->ultimaMovimentacao = date('Y-m-d');
            $solicitacao->status = $status;
            $solicitacao->save();

            // O card que originou as duas linhas abaixo continha as seguintes palavras:
            // Os botões de devolver e encerrar eles permitem a ação, porém NÃO REMOVE aluno da rota, ele permanece na rota mesmo após a devolução/encerramento, ou seja os botões em parte não funcionam.
            // O botão de devolver/encerrar/excluir (excluir ainda não testado, tem que testar) não remove aluno da rota.

            PontoAluno::removerTodasRotas($solicitacao->idAluno);
            HistoricoMovimentacaoRota::deleteAll(['idSolicitacaoTransporte' => $solicitacao->id]);

            // Fim da alteração (19/04/2020)


            return ['status' => true];
        } else {
            return $this->renderAjax('statusAjax', [
                'model' => $model,
                'solicitacao' => $solicitacao,
                'action' => 'solicitacao-transporte/alteracao-status-ajax',
                'status' => $status,
                'alterarDadosProtegidos' => $alterarDadosProtegidos
            ]);
        }
    }

    public function actionAlteracaoStatusAjax($status)
    {
        $model = new SolicitacaoStatus();
        $id =  Yii::$app->request->get('id');
        $status =  Yii::$app->request->get('status');
        if ($id) {
            $model->idSolicitacaoTransporte = $id;
            $solicitacao = $this->findModel($id);
        }

        // CASO SEJA UMA SOL DE CANCELAMENTO COM STATUS PARA SER DEFERIDO!
        // PRECISAMOS TER UM STATUS ESPECÍFICO PARA SOL DE CANCELAMENTO 
        // https://trello.com/c/ZZ0cxnoG/95-nomenclatura-de-status-do-cancelamento-de-benef%C3%ADcio-5pts
        if ($status && $status == SolicitacaoTransporte::STATUS_DEFERIDO && $solicitacao->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO) {
            $status = SolicitacaoTransporte::STATUS_CANCELADO;
        }

        if ($status) {
            $model->status = $status;
        }


        if (Usuario::permissao(Usuario::PERFIL_DIRETOR) && $model->status == SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR) {
            $model->justificativa = 'Eu ' . \Yii::$app->User->identity->nome . ', portador do RG ' . \Yii::$app->User->identity->rg . ', declaro, nesta data, ter ciência e estar de acordo com os procedimentos realizados quanto a solicitação de Transporte Público Escolar do (a) aluno (a) à luz dos critérios de elegibilidade com base na Lei Municipal nº 8.107, de 03 de maio de 2010 e Lei Federal nº 12.796, de 04 de abril de 2013. Comprometo-me a respeitá-los e cumpri-los plena e integralmente, além de manter sempre verossímeis os dados da instituição e de minha área de competência. Respondendo administrativa, civil e penalmente, pela inclusão de informações inadequadas, se comprovada a omissão ou comissão, dolo ou culpa, nos termos da Lei Federal nº 8.429, de 02 de junho de 1992, que dispõe sobre as sanções aplicáveis aos agentes públicos no exercício de mandato, cargo, emprego ou função na administração pública direta, indireta ou funcional.';
        }
        if ($model->load(Yii::$app->request->post())) {

            $model->idUsuario = \Yii::$app->User->identity->id;
            $model->dataCadastro = date('Y-m-d');
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (!$model->save()) {
                return ['status' => false, 'errors' => $model->getErrors()];
            }
            $solicitacao->ultimaMovimentacao = date('Y-m-d');


            $solicitacao->status = $status;
            $solicitacao->save();


            if ($status == SolicitacaoTransporte::STATUS_DEFERIDO || $status ==  SolicitacaoTransporte::STATUS_CANCELADO) {
                // REMOVE DE TODAS AS ROTAS!!!!
                PontoAluno::removerTodasRotas($solicitacao->idAluno);
                $solicitacoesAntigas = SolicitacaoTransporte::find()->where(['idAluno' => $solicitacao->idAluno])->andWhere(['<>', 'id', $solicitacao->id])->all();

                foreach ($solicitacoesAntigas as $solicitacaoAntiga) {
                    $solicitacaoAntiga->status = SolicitacaoTransporte::STATUS_ENCERRADA;
                    $solicitacaoAntiga->ultimaMovimentacao = date('Y-m-d');
                    $solicitacaoAntiga->save();
                    $modelStatus = new SolicitacaoStatus();
                    $modelStatus->idUsuario = \Yii::$app->User->identity->id;
                    $modelStatus->dataCadastro = date('Y-m-d');
                    $modelStatus->status = SolicitacaoTransporte::STATUS_ENCERRADA;
                    $modelStatus->idSolicitacaoTransporte = $solicitacaoAntiga->id;
                    $modelStatus->justificativa = 'ENCERRADO PELO SISTEMA. NOVA SOLICITAÇÃO VIGENTE #' . $solicitacao->id;
                    $modelStatus->save();
                }
            }

            return ['status' => true];
        } else {
            return $this->renderAjax('statusAjax', [
                'model' => $model,
                'solicitacao' => $solicitacao,
                'action' => 'solicitacao-transporte/alteracao-status-ajax',
                'status' => $status,
            ]);
        }
    }

    public function actionViewSolicitacaoAjax($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $aluno =  Aluno::findOne(['id' => $id]);
        return $aluno->solicitacao;
    }

    /**
     * Creates a new SolicitacaoTransporte model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SolicitacaoTransporte();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    // function getDrivingDistance($lat1, $lat2, $long1, $long2)
    // {
    //   $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pt-BR&key=AIzaSyCLdXxxtVSN5I0NA2WJ2buip_pEwfF2pW0";
    //   $ch = curl_init();
    //   curl_setopt($ch, CURLOPT_URL, $url);
    //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //   curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //   $response = curl_exec($ch);
    //   curl_close($ch);

    //   $response_a = json_decode($response, true);

    //   $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //   $distanceValue = $response_a['rows'][0]['elements'][0]['distance']['value'];
    //   $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

    //   return array('distance' => $dist, 'time' => $time, 'distanceValue' => $distanceValue);
    // }

    // private function getDistancia($model){

    //   $distancia = $this->getDrivingDistance($model->aluno->lat,$model->escola->lat,$model->aluno->lng,$model->escola->lng);

    //   if($distancia)
    //     return $distancia['distanceValue'] / 1000;

    //   return null;
    //   //https://maps.googleapis.com/maps/api/directions/json?origin=Disneyland&destination=Universal+Studios+Hollywood&key=YOUR_API_KEY

    // }

    private function escolasProximas($model)
    {

        $distanciaKm = Yii::$app->distanceMatrix->localDistance(
            $model->aluno->lat,
            $model->aluno->lng,
            $model->aluno->escola->lat,
            $model->aluno->escola->lng
        );


        //ionic cordova run android -l -c -s --address  172.16.121.14
        $escolas = Escola::escolasProximas(
            $model->aluno->lat,
            $model->aluno->lng,
            $distanciaKm,
            $model->aluno->escola->tipo,
            $model->aluno->escola->id
        );
        return $escolas;
        //  $trajeto = Yii::$app->distanceMatrix->singleRoute(
        //   [
        //   ['lat' => $model->aluno->lat, 'lng' => $model->aluno->lng],
        //   ],
        //   [
        //   ['lat' => $model->aluno->escola->lat, 'lng' => $model->aluno->escola->lng],
        //   ]
        //   );

        //  $escolas = [];
        //   print_r($trajeto);
        //  if($trajeto){
        //   $distanciaKm = $trajeto['distanceValue'] / 1000;
        //   $escolas = Escola::escolasProximas(
        //     $model->aluno->lat,
        //     $model->aluno->lng,
        //     $distanciaKm,
        //     $model->aluno->escola->tipo,
        //     $model->aluno->escola->id
        //     );
        // }
        // return $escolas;
    }
    private function createMap($model)
    {


        $residenciaAluno = new LatLng(['lat' => $model->aluno->lat, 'lng' => $model->aluno->lng]);

        $escolaAluno = new LatLng(['lat' => $model->aluno->escola->lat, 'lng' => $model->aluno->escola->lng]);

        $map = new Map([
            'center' => $residenciaAluno,
            'zoom' => 15,
        ]);



        $marker = new Marker([
            'position' => $residenciaAluno,
            'title' => 'Residência do(a) aluno(a)',
            'icon' => 'img/icon_residencia.png',
        ]);
        $marker->attachInfoWindow(
            new InfoWindow([
                'content' => '<p>Residência de ' . $model->aluno->nome . '</p>'
            ])
        );
        $map->addOverlay($marker);
        $marker = new Marker([
            'position' => $escolaAluno,
            'title' => 'Escola do(a) aluno(a)',
            'icon' => 'img/icon_escola.png'
        ]);
        $marker->attachInfoWindow(
            new InfoWindow([
                'content' => '<p>Escola de ' . $model->aluno->nome . ' <br>' . $model->aluno->escola->nome . '</p>'
            ])
        );
        $map->addOverlay($marker);

        $escolas = $this->escolasProximas($model);

        foreach ($escolas as $escola) {
            $marker = new Marker([
                'position' => new LatLng(['lat' => $escola->lat, 'lng' =>  $escola->lng]),
                'title' => $escola->nomeCompleto,
                'icon' => 'img/icon_escola.png'
            ]);
            $marker->attachInfoWindow(
                new InfoWindow([
                    'content' => '<p>' . $escola->nomeCompleto . '</p>'
                ])
            );
            $map->addOverlay($marker);
        }


        return $map;
    }

    // public function actionTeste(){
    // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    // $aluno = Aluno::findOne(99);


    //     // print '<pre>';
    //     // print_r($trajeto);
    //     // print '</pre>';
    //     // exit(1);
    //     //print_r($escolas[0]);
    //     if($trajeto){
    //         $distanciaKm = $trajeto['distanceValue'] / 1000;
    //         $escolas = Escola::escolasProximas($aluno->lat, $aluno->lng, $distanciaKm);
    //     }


    //     return [
    //         'trajeto' => $trajeto,
    //         'aluno'   => Yii::$app->arrayPicker->pick([$aluno], ['lat','lng']),
    //         'escola'  => Yii::$app->arrayPicker->pick([$aluno], ['lat','lng']),
    //         'escolas' => Yii::$app->arrayPicker->pick($escolas, ['id','nome','endereco','lat','lng']),
    //     ];


    // }

    public function actionMapa()
    {
        $model = new SolicitacaoTransporte();
        if (Yii::$app->request->get('idAluno')) {
            $model->idAluno = Yii::$app->request->get('idAluno');
        }

        if (Yii::$app->request->get('idEscola')) {
            $model->idEscola = Yii::$app->request->get('idEscola');
        }


        return $this->renderAjax('mapa', [
            'mapa' =>  $this->createMap($model),
        ]);
    }

    public function actionCreateAjax()
    {
        $model = new SolicitacaoTransporte();
        $model->data = date('Y-m-d');
        $model->status = SolicitacaoTransporte::STATUS_ANDAMENTO;
		
		
        // print_r($ponto);
		
        if (Yii::$app->request->get('idAluno'))
            $model->idAluno = Yii::$app->request->get('idAluno');


        if (Yii::$app->request->get('idEscola'))
            $model->idEscola = Yii::$app->request->get('idEscola');

        if (Yii::$app->request->get('tipoSolicitacao')) {
            $model->tipoSolicitacao = Yii::$app->request->get('tipoSolicitacao');
            if ($model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO) {
                $solicitacaoAtiva = SolicitacaoTransporte::find()->orWhere(['status' => SolicitacaoTransporte::STATUS_DEFERIDO])->orWhere(['status' => SolicitacaoTransporte::STATUS_CONCEDIDO])->orWhere(['status' => SolicitacaoTransporte::STATUS_ATENDIDO])->andWhere(['idAluno' => $model->idAluno])->orderBy(['status' => SORT_DESC])->one();
                if (!$solicitacaoAtiva) {
                    //return '<b>Não há solicitações para serem canceladas.</b>';
                }
            }
        }
		
		
		

        if ($model->load(Yii::$app->request->post())) {
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			
			$post = Yii::$app->request->post();
			//alteração 12/11/2021 - qualquer perfil poderá cancelar beneficio
			if ($post['SolicitacaoTransporte']['tipoSolicitacao'] == 2) {
				$sql = "select st.id from SolicitacaoTransporte st where (st.`status` = ".SolicitacaoTransporte::STATUS_CONCEDIDO." or 	st.`status` = ".SolicitacaoTransporte::STATUS_ANDAMENTO." or 	st.`status` = ".SolicitacaoTransporte::STATUS_DEFERIDO."  or 	st.`status` = ".SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR." or 	st.`status` = ".SolicitacaoTransporte::STATUS_DEFERIDO_DRE." or 	st.`status` = ".SolicitacaoTransporte::STATUS_ATENDIDO." ) 	and st.idAluno = ".$model->idAluno;
				$dados = Yii::$app->getDb()->createCommand($sql)->queryAll();	
				if($dados[0]['id']){
					$solicitacao = SolicitacaoTransporte::findOne($dados[0]['id']);
					$solicitacao->status =SolicitacaoTransporte::STATUS_ENCERRADA;    
					$solicitacao->save();					
					PontoAluno::removerTodasRotas($model->id);					
					$modelStatus = new SolicitacaoStatus();
					$modelStatus->idUsuario = \Yii::$app->User->identity->id;
					$modelStatus->dataCadastro = date('Y-m-d');
					$modelStatus->status = SolicitacaoTransporte::STATUS_ENCERRADA;
					$modelStatus->idSolicitacaoTransporte = $dados[0]['id'];
					$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
					$modelStatus->mostrar = 1;							
					$modelStatus->justificativa = 'BENEFÍCIO ENCERRADO VIA SISTEMA';
					$modelStatus->save();
					 return ['status' => true];
				}
			}
            
            //copia dados da solicitacao
            $solicitacaoAtiva = SolicitacaoTransporte::find()->orWhere(['status' => SolicitacaoTransporte::STATUS_DEFERIDO])->orWhere(['status' => SolicitacaoTransporte::STATUS_ATENDIDO])->andWhere(['idAluno' => $model->idAluno])->one();

            if ($model->novaSolicitacao == SolicitacaoTransporte::RENOVACAO && $model->tipoSolicitacao != SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO) {
                $model->setAttributes($solicitacaoAtiva->attributes);
                $model->novaSolicitacao =  SolicitacaoTransporte::RENOVACAO;
                $model->id = null;
                $model->data = date('Y-m-d');
                $model->status = $solicitacaoAtiva->status;
                // $model->idRotaIda = $solicitacaoAtual->idRotaIda;
                // $model->idRotaVolta = $solicitacaoAtual->idRotaVolta;
            }
            $model->anoVigente = $this->configuracao->calcularAno();

            // campos que devem ser atualizados na tabela de aluno
            $aluno = Aluno::findOne($model->idAluno);
            if (Yii::$app->request->post('aluno-ensino')) {
                $aluno->ensino = Yii::$app->request->post('aluno-ensino');
            }
            if (Yii::$app->request->post('aluno-serie')) {
                $aluno->serie = Yii::$app->request->post('aluno-serie');
            }
            if (Yii::$app->request->post('aluno-turma')) {
                $aluno->turma = Yii::$app->request->post('aluno-turma');
            }
            $aluno->save(false);

            if ($model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO) {
                $model->modalidadeBeneficio = $solicitacaoAtiva->modalidadeBeneficio;
                // pendenga
            }


            if ($model->save()) {
                //Se é uma solicitação de renovação
                // E não é sol de cancelamento
                if ($model->novaSolicitacao == SolicitacaoTransporte::RENOVACAO && $model->tipoSolicitacao != SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO) {




                    // cria o status atendido
                    $modelStatus = new SolicitacaoStatus();
                    $modelStatus->idCondutorRota = $model->rotaIda->id;
                    $modelStatus->idEscola = $model->idEscola;
                    $modelStatus->idAluno = $model->idAluno;
                    if ($model->idRotaIda) {
                        $modelStatus->idCondutor = $model->rotaIda->idCondutor;
                        $modelStatus->idCondutorRota = $model->idRotaIda;
                        $modelStatus->idVeiculo = $model->rotaIda->condutor->idVeiculo;
                        SolicitacaoTransporte::logRotaIdaInsercao($model);
                    }
                    if ($model->idRotaVolta) {
                        $modelStatus->idCondutor = $model->rotaVolta->idCondutor;
                        $modelStatus->idCondutorRota = $model->idRotaVolta;
                        $modelStatus->idVeiculo = $model->rotaVolta->condutor->idVeiculo;
                        SolicitacaoTransporte::logRotaVoltaInsercao($model);
                    }
                    $modelStatus->idUsuario = \Yii::$app->User->identity->id;
                    $modelStatus->dataCadastro = date('Y-m-d');
                    $modelStatus->status = SolicitacaoTransporte::STATUS_ATENDIDO;
                    $modelStatus->idSolicitacaoTransporte = $model->id;
                    $modelStatus->justificativa = 'RENOVAÇÃO. SOLICITAÇÃO ANTERIOR #' . $solicitacaoAtiva->id . '.';
                    $modelStatus->save();

                    // mata solicitações antigas
                    $solicitacoes = SolicitacaoTransporte::find()->where(['idAluno' => $model->idAluno])->andWhere(['<>', 'id', $model->id])->all();
                    foreach ($solicitacoes as $solicitacao) {
                        $solicitacao->status = SolicitacaoTransporte::STATUS_ENCERRADA;
                        if ($solicitacao->idRotaIda) {
                            SolicitacaoTransporte::logRotaIda($solicitacao);
                            $solicitacao->idRotaIda = null;
                        }
                        if ($solicitacao->idRotaVolta) {
                            SolicitacaoTransporte::logRotaVolta($solicitacao);
                            $solicitacao->idRotaVolta = null;
                        }

                        $solicitacao->save();
                        $modelStatus = new SolicitacaoStatus();
                        $modelStatus->idUsuario = \Yii::$app->User->identity->id;
                        $modelStatus->dataCadastro = date('Y-m-d');
                        $modelStatus->status = SolicitacaoTransporte::STATUS_ENCERRADA;
                        $modelStatus->idSolicitacaoTransporte = $solicitacao->id;
                        $modelStatus->justificativa = 'FINALIZADO PARA RENOVAÇÃO. NOVA SOLICITAÇÃO VIGENTE #' . $model->id . '.';
                        $modelStatus->save();
                    }
                }
                $this->salvarEscolas($model->EscolasProximas, $model);
                $this->uploadMultiple($model);
                if ($model->modalidadeBeneficio == Aluno::MODALIDADE_FRETE && !$model->distanciaEscola) {
                    $model->distanciaEscola = Yii::$app->distanceMatrix->localDistance(
                        $model->aluno->lat,
                        $model->aluno->lng,
                        $model->escola->lat,
                        $model->escola->lng
                    );
                    $model->ultimaMovimentacao = date('Y-m-d');
                    $model->save();
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao salvar solicitação de transporte',
                    'id' => $model->id,
                    'validation' => $model->getErrors()
                ];
            }
            return ['status' => true];
        } else {
			
			if (Yii::$app->request->get('tipoSolicitacao') == 1) {
				$label ='Criar';
			}else{
				$label ='Encerrar';
			}
            // if($model->getErrors()){
            //     print_r( $model->getErrors() );
            // }
            // if($model->getErrors())
            //     Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header'=>'Erro ao salvar.']));
            return $this->renderAjax('_formAjax', [
                'model' => $model,
				'label' => $label,
                'action' => 'solicitacao-transporte/create-ajax',
                'escolas' => $this->escolasProximas($model),
            ]);
        }
    }

    private function salvarEscolas($input, $model)
    {
        foreach ($input as $key => $value) {
            $modelEscola = new SolicitacaoTransporteEscolas();
            $modelEscola->idSolicitacaoTransporte = $model->id;
            $modelEscola->idEscola = $value;
            if (!$modelEscola->save()) {
                \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar Escolas próximas');
            }
        }
    }


    private function uploadMultiple($model)
    {
        $this->actionUploadFile($model, 'documentoComprovanteEndereco', TipoDocumento::TIPO_COMPROVANTE_ENDERECO);
        $this->actionUploadFile($model, 'documentoDeclaracaoVizinho', TipoDocumento::TIPO_DECLARACAO_VIZINHOS);
        $this->actionUploadFile($model, 'documentoLaudoMedico', TipoDocumento::TIPO_LAUDO_MEDICO);
        $this->actionUploadFile($model, 'documentoTransporteEspecial', TipoDocumento::TIPO_DECLARACAO_TRANSPORTE_ESPECIAL);
        $this->actionUploadFile($model, 'documentoInexistenciaVaga', TipoDocumento::TIPO_DECLARACAO_INEXISTENCIA_VAGA);
        $this->actionUploadFile($model, 'documentoFormalizacaoSolicitacao', TipoDocumento::TIPO_FORMALIZACAO_SOLICITACAO);
    }
    private function actionUploadFile($model, $file, $idTipoDocumento)
    {

        $arquivos = UploadedFile::getInstances($model, $file);

        if ($arquivos) {
            //print 'DELETED '.$idTipoDocumento;
            DocumentoSolicitacao::deleteAll(['idSolicitacaoTransporte' => $model->id, 'idTipo' => $idTipoDocumento]);

            $dirBase = Yii::getAlias('@webroot') . '/';
            $dir = 'arquivos/' . $idTipoDocumento . '/';

            if (!file_exists($dirBase . $dir))
                mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo) {
                $nomeArquivo = $idTipoDocumento . '_' . time() . '_' . $i . '.' . $arquivo->extension;
                $arquivo->saveAs($dirBase . $dir . $nomeArquivo);

                $modelDocumento = new DocumentoSolicitacao();
                $modelDocumento->nome = $nomeArquivo;
                $modelDocumento->idSolicitacaoTransporte = $model->id;
                $modelDocumento->arquivo = $dir . $nomeArquivo;
                $modelDocumento->idTipo = $idTipoDocumento;
                $modelDocumento->dataCadastro = date('Y-m-d H:i:s');
                $modelDocumento->save();

                $i++;
            }
        }
    }


    public function actionDeferir($id)
    {
        $model = $this->findModel($id);
        if (
            $model->status == SolicitacaoTransporte::STATUS_ANDAMENTO &&
            Yii::$app->user->identity->idPerfil == Usuario::PERFIL_DIRETOR
        ) {
            $model->status = ($model->escola->tipo == Escola::TIPO_EE) ? SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR : SolicitacaoTransporte::STATUS_DEFERIDO;
            $model->idDiretor = Yii::$app->user->identity->id;
            $model->dataStatusDiretor = date('Y-m-d h:i:s');
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header' => 'Erro ao aprovar solicitação.']));
            }
        }
        if (
            $model->status == SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR &&
            Yii::$app->user->identity->idPerfil == Usuario::PERFIL_DRE &&
            $model->escola->tipo == Escola::TIPO_EE
        ) {
            $model->status = SolicitacaoTransporte::STATUS_DEFERIDO;
            $model->idDre = Yii::$app->user->identity->id;
            $model->dataStatusDre = date('Y-m-d h:i:s');
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header' => 'Erro ao aprovar solicitação.']));
            }
        }

        if ($model->status == SolicitacaoTransporte::STATUS_DEFERIDO) {
            $model->status = SolicitacaoTransporte::STATUS_DEFERIDO;
            $model->idDre = Yii::$app->user->identity->id;
            $model->dataStatusDre = date('Y-m-d h:i:s');
        }
        //PAREI AQUI
        return $this->redirect(['index']);
    }

    public function actionIndeferir($id)
    {
        $model = $this->findModel($id);
        if (
            $model->status == SolicitacaoTransporte::STATUS_ANDAMENTO &&
            Yii::$app->user->identity->idPerfil == Usuario::PERFIL_DIRETOR
        ) {
            $model->status = SolicitacaoTransporte::STATUS_INDEFERIDO;
            $model->idDiretor = Yii::$app->user->identity->id;
            $model->dataStatusDiretor = date('Y-m-d h:i:s');
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header' => 'Erro ao aprovar solicitação.']));
            }
        }
        if (
            $model->status == SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR &&
            Yii::$app->user->identity->idPerfil == Usuario::PERFIL_DRE &&
            $model->escola->tipo == Escola::TIPO_EE
        ) {
            $model->status = SolicitacaoTransporte::STATUS_INDEFERIDO;
            $model->idDre = Yii::$app->user->identity->id;
            $model->dataStatusDre = date('Y-m-d h:i:s');
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header' => 'Erro ao aprovar solicitação.']));
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing SolicitacaoTransporte model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->uploadMultiple($model);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SolicitacaoTransporte model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionSolicitacoesPendentes()
    {


        $response = SolicitacaoTransporte::agruparSolicitacoesPendentesPorEscola();
        $escolasArr = $response['escolasArr'];
        $totaisArr = $response['totaisArr'];
        $escolas = $response['escolas'];



        return $this->render('solicitacoes-pendentes', [
            'escolas' => $escolas,
            'escolasArr' => $escolasArr,
            'totaisArr' => $totaisArr,
        ]);



        // - Considerar apenas as solicitações de Benefício e Cancelamento com status Andamento e Deferido pela Diretor (para escolas vinculadas a Diretoria Regional de Ensino / Tipo “EE”);

        // - Exibir os dados em tela, com as seguintes colunas (da esquerda para a direita): Escola, Benefício / Andamento, Benefício / Deferido pela Diretor, Cancelamento / Andamento, Cancelamento / Deferido pela DRE e Total;

        // - Os dados exibidos são quantitativos (apenas números);

        // - A coluna Total deve exibir a somatória das colunas Benefício / Andamento, Benefício / Deferido pela Diretor, Cancelamento / Andamento e Cancelamento / Deferido pela Diretor;

        // - Exibir Todas as Escolas (Municipal, Estadual, Filantrópica) que possuam, ao menos, 1 (uma) Solicitação que se enquadre nos cenários apresentados;

        // - Exibir Totalizador por “Tipo” de Solicitação (Benefício/Cancelamento) / Solicitações Pendentes (Benefício) – Soma das Colunas Benefício/Andamento e Benefício/Deferido pela Diretor e Solicitações Pendentes (Cancelamento) – Soma das Colunas Cancelamento/Andamento e Cancelamento/Deferido pela Diretor;

        // - Exibir Totalizador Geral / Total de Solicitações Pendentes;

        // - Disponibilizar Exportação de Dados nos formatos já disponíveis na Plataforma com as seguintes colunas (da esquerda para a direita): Escola, Benefício / Andamento, Benefício / Deferido pela Diretor, Cancelamento / Andamento, Cancelamento / Deferido pela DRE e Total. Exibir abaixo a Data de Emissão do Relatório.

    }

    public function actionExportSolicitacoesPendentes($tipo)
    {

        // $content = '<table>';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // $sheet->setCellValue('A1:N5', 'Logo');
        $left = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            )
        );
        $right = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            )
        );
        $center = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );

        $borderHard = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $borderSoft = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath(Yii::getAlias('@webroot') . '/img/brasaoPdf.png'); // put your path and image here
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(50);
        $drawing->setOffsetY(15);
        $drawing->setRotation(0);
        $drawing->setWorksheet($sheet);

        $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing2->setPath(Yii::getAlias('@webroot') . '/img/faixa.png'); // put your path and image here
        $drawing2->setCoordinates('C1');
        $drawing2->setOffsetX(0);
        $drawing2->setOffsetY(0);
        $drawing2->setRotation(0);
        $drawing2->setWorksheet($sheet);


        // SETUP DAS COLUNAS
        $sheet->getColumnDimension('A')->setWidth(40.14);
        $sheet->getColumnDimension('B')->setWidth(15.5);
        $sheet->getColumnDimension('C')->setWidth(35.34);
        $sheet->getColumnDimension('D')->setWidth(15.14);
        $sheet->getColumnDimension('E')->setWidth(25.57);
        $sheet->getColumnDimension('F')->setWidth(10.57);




        //
        $i = 1;
        // PRÓXIMA LINHA
        $sheet->mergeCells('A' . $i . ':B' . ($i + 4));
        $sheet->mergeCells('C' . $i . ':F' . $i);
        $sheet->mergeCells('C' . ($i + 1) . ':F' . ($i + 1));
        $sheet->setCellValue('C' . ($i + 1), "Secretaria de Educação e Cidadania");
        $sheet->getStyle('C' . ($i + 1))->applyFromArray($left)->getFont()->setBold(true);

        $sheet->mergeCells('C' . ($i + 2) . ':F' . ($i + 4));
        $sheet->setCellValue('C' . ($i + 2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
        $sheet->getStyle('C' . ($i + 2))->getAlignment()->setWrapText(true);

        $sheet->getStyle('A' . ($i + 2) . ':F' . ($i + 2))->applyFromArray($left);


        $i += 5;
        $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray($center);

        $sheet->setCellValue('A' . $i, "NOME");
        $sheet->setCellValue('B' . $i, "BENEFÍCIO / ANDAMENTO");
        $sheet->setCellValue('C' . $i, "BENEFÍCIO / DEFERIDO PELO DIRETOR");
        $sheet->setCellValue('D' . $i, "CANCELAMENTO / ANDAMENTO");
        $sheet->setCellValue('E' . $i, "CANCELAMENTO / DEFERIDO PELO DIRETOR");
        $sheet->setCellValue('F' . $i, "TOTAL");

        $sheet->getStyle('A' . $i . ':F' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $i . ':F' . $i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF000000');

        $sheet->getStyle('A' . $i . ':F' . $i)->getFont()->setBold(true);

        $sheet->getStyle('A' . $i . ':F' . $i)->getFont()->setColor($colorWhite);
        $sheet->setAutoFilter('A' . $i . ':F' . $i);

        $response = SolicitacaoTransporte::agruparSolicitacoesPendentesPorEscola();
        $escolasArr = $response['escolasArr'];
        $totaisArr = $response['totaisArr'];
        $escolas = $response['escolas'];

        foreach ($escolas as $model) {
            $i++;
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':F' . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F6F6F6');
            }
            // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
            $sheet->getStyle('B' . $i . ':F' . $i)->applyFromArray($center);
            $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray($borderSoft);
            $sheet->getStyle('A' . $i . ':F' . $i)
                ->getAlignment()->setWrapText(true);

            $sheet->setCellValue('A' . $i, ' ' . $model->nomeCompleto);
            $sheet->setCellValue('B' . $i,  $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['BENEFICIO']);
            $sheet->setCellValue('C' . $i,  $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['BENEFICIO']);
            $sheet->setCellValue('D' . $i,  $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['CANCELAMENTO']);
            $sheet->setCellValue('E' . $i,  $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['CANCELAMENTO']);
            $sheet->setCellValue('F' . $i,  $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['BENEFICIO'] +
                $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['BENEFICIO'] +
                $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['CANCELAMENTO'] +
                $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['CANCELAMENTO']);
        }
        $i++;
        if ($i % 2 == 0) {
            $sheet->getStyle('A' . $i . ':F' . $i)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F6F6F6');
        }
        // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
        $sheet->getStyle('B' . $i . ':F' . $i)->applyFromArray($center);
        $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray($borderSoft);
        $sheet->getStyle('A' . $i . ':F' . $i)
            ->getAlignment()->setWrapText(true);

        $sheet->getStyle('A' . $i . ':E' . $i)->applyFromArray($right);
        $sheet->mergeCells('A' . $i . ':E' . $i);
        $sheet->setCellValue('A' . $i, 'TOTAL ');
        $sheet->setCellValue('F' . $i, $totaisArr['TOTAL']);


        $i++;
        $sheet->mergeCells('A' . $i . ':F' . $i);

        $sheet->getStyle('B' . $i . ':F' . $i)->applyFromArray($center);
        $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray($borderSoft);
        $sheet->getStyle('A' . $i . ':F' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->setCellValue('A' . $i, 'Emitido em ' . date("d/m/Y H:i") . '');

        $base = "arquivos/_exportacoes/";

        switch ($tipo) {
            case 'PDF':
                try {
                    $filename = $base . "Solicitacoes_Aguardando_Atendimento_" . date('d-m-Y-H-i-s') . ".pdf";

                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                    $writer->setPreCalculateFormulas(false);
                    $writer->save($filename);

                    header("Content-Disposition: attachment; filename=" . $filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                } catch (\Exception $e) {
                    exit($e->getMessage());
                }

                break;
            case 'TXT':

                $filename = $base . 'Solicitacoes_Aguardando_Atendimento_' . date('d-m-Y-H-i-s') . '.txt';
                $fp = fopen($filename, 'a');
                $query = SolicitacaoTransporte::find()->orderBy(['nome' => SORT_ASC]);

                $response = SolicitacaoTransporte::agruparSolicitacoesPendentesPorEscola();
                $escolasArr = $response['escolasArr'];
                $totaisArr = $response['totaisArr'];
                $escolas = $response['escolas'];

                foreach ($escolas as $model) {
                    $l = '';
                    $l .= $model->nomeCompleto;
                    $l .= ';' . $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['BENEFICIO'];
                    $l .= ';' . $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['BENEFICIO'];
                    $l .= ';' . $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['CANCELAMENTO'];
                    $l .= ';' . $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['CANCELAMENTO'];
                    $l .= ';' . $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['BENEFICIO'] +
                        $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['BENEFICIO'] +
                        $GLOBALS['escolasArr'][$model->id]['STATUS_DEFERIDO_DIRETOR']['CANCELAMENTO'] +
                        $GLOBALS['escolasArr'][$model->id]['STATUS_ANDAMENTO']['CANCELAMENTO'];
                    $l .= '';
                    fwrite($fp, $l);
                }
                fclose($fp);
                try {
                    // $writer = new Xlsx($spreadsheet);
                    header("Content-Disposition: attachment; filename=" . $filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                } catch (\Exception $e) {
                    exit($e->getMessage());
                }
                break;
            case 'EXCEL':
                try {
                    $writer = new Xlsx($spreadsheet);
                    $filename = $base . "Condutores_" . date('d-m-Y-H-i-s') . ".xlsx";
                    $writer->save($filename);
                    header("Content-Disposition: attachment; filename=" . $filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                } catch (\Exception $e) {
                    exit($e->getMessage());
                }
                break;
        }
    }
    public function actionSolicitacoesAguardandoAtendimento()
    {
        $solicitacoesPermitidas = [];
        $solicitacoes = SolicitacaoTransporte::find()
            ->andWhere(['novaSolicitacao' => SolicitacaoTransporte::NOVA_SOLICITACAO])
            ->andWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_INDEFERIDO])
            ->andWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
			->andWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_CONCEDIDO])
            //->andWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO]) // alteracao mauro 21/10/2021
            ->andWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ANDAMENTO])
            ->andWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ENCERRADA])
            ->all();
			


        //Antigo forçar mostrar só as do grupo
        //$solicitacoesPermitidas = UsuarioGrupo::solicitacoesPermitidas($solicitacoes);
        foreach ($solicitacoes as $solicitacao) {
            // trata o caso de não poder aparecer solicitações com status deferido pelo diretor
            // em caso de escola estadual
            if ($solicitacao->status != SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR || $solicitacao->escola->tipo != Escola::TIPO_EE) {
                // Solicitações def diretor e com tipo EE
                // SE EU SOU PERFIL DISTR. EU APLICO A REGRA DE GRUPO
                if (Usuario::permissao(Usuario::PERFIL_TESC_DISTRIBUICAO)) {
                    if (UsuarioGrupo::autorizarSolicitacao($solicitacao))
                        $solicitacoesPermitidas[] = $solicitacao->id;
                } else {
                    $solicitacoesPermitidas[] = $solicitacao->id;
                }
            }
        }

        $searchModel = new SolicitacaoTransporteSearch();
		
		
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $solicitacoesPermitidas);
        $dataProvider->sort->defaultOrder = ['id' => SORT_ASC];
        $dataProvider->query->andFilterWhere(['novaSolicitacao' => SolicitacaoTransporte::NOVA_SOLICITACAO]);
        $dataProvider->query->andFilterWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_INDEFERIDO]);
        $dataProvider->query->andFilterWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO]);
		$dataProvider->query->andFilterWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_CONCEDIDO]);
        //$dataProvider->query->andFilterWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO]); // alteracao mauro 21/10/2021
        $dataProvider->query->andFilterWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ANDAMENTO]);
        $dataProvider->query->andFilterWhere(['<>', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ENCERRADA]);

        //forçar não printar nada quando não tem permissão 
        if (!Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::TESC_CONSULTA, Usuario::PERFIL_DRE, Usuario::PERFIL_DIRETOR, Usuario::PERFIL_SECRETARIO, Usuario::PERFIL_TESC_PASSE_ESCOLAR]) && !$solicitacoesPermitidas) {
            $dataProvider->query->andFilterWhere(['Escola.id' => 99999]);
        }

        if (Usuario::permissao(Usuario::PERFIL_TESC_PASSE_ESCOLAR)) {
            $dataProvider->query->andFilterWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE]);
        }

        // if(Usuario::permissao(Usuario::PERFIL_DIRETOR) )
        //   $dataProvider->query->andFilterWhere(['idEscola' => 1739]);

        if (Usuario::permissao(Usuario::PERFIL_DRE))
            $dataProvider->query->andFilterWhere(['Escola.unidade' => Escola::UNIDADE_ESTADUAL]);

        if (Usuario::permissao(Usuario::PERFIL_DIRETOR)) {
            $ids = EscolaDiretor::listaEscolas();
            $ids[] = 999999;
            $dataProvider->query->andFilterWhere(['in', 'Escola.id', $ids]);
        }

        if (Usuario::permissao(Usuario::PERFIL_SECRETARIO)) {
            $ids = EscolaSecretario::listaEscolas();
            $ids[] = 999999;
            $dataProvider->query->andFilterWhere(['in', 'Escola.id', $ids]);
        }
        // cast list object in dataprovider
        // $dataProvider->query->join('aluno')->andFilterWhere(['status'=>1]); 
        // $m = $dataProvider->getModels();
        // $records = [];

        $dataProvider->pagination = ['pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20];

        return $this->render('solicitacoes-aguardando-atendimento', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSolicitacaoVigenteAjax($idAluno)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $solicitacao = SolicitacaoTransporte::find()
                ->andwhere(['=', 'idAluno', $idAluno])
                ->andWhere(['<>', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
                ->andWhere(['=', 'status', SolicitacaoTransporte::STATUS_ATENDIDO])
                ->orderBy(['status' => SORT_DESC])
                ->one();

            if (!$solicitacao) {
                $solicitacao = SolicitacaoTransporte::find()
                    ->andwhere(['=', 'idAluno', $idAluno])
                    ->andWhere(['<>', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
                    ->andWhere(['=', 'status', SolicitacaoTransporte::STATUS_CONCEDIDO])
                    ->orderBy(['status' => SORT_DESC])
                    ->one();
            }

            if (!$solicitacao)
                throw new NotFoundHttpException();

            return [
                'status' => true,
                'solicitacao' => $solicitacao,
                'escola' => $solicitacao->escola,
                'aluno' => $solicitacao->aluno
            ];
        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
    }

    public function actionUpdateBarreiraFisicaAjax($idSolicitacao)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $solicitacao = SolicitacaoTransporte::findOne($idSolicitacao);

            if (!$solicitacao) {
                return [
                    'status' => false
                ];
            }
            
            $solicitacao->barreiraFisica = Yii::$app->request->post('barreiraFisica');
            if ($solicitacao->save())
            {
                return [
                    'status' => true,
                    'solicitacao' => $solicitacao,
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'solicitacao' => $solicitacao,
                    'error' => print_r($solicitacao->getErrors(), true)
                ];
            }
        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
    }

    public function actionUpdateMotivoBarreiraFisicaAjax($idSolicitacao)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $solicitacao = SolicitacaoTransporte::findOne($idSolicitacao);

            if (!$solicitacao) {
                return [
                    'status' => false
                ];
            }
            
            $solicitacao->motivoBarreiraFisica = Yii::$app->request->post('motivoBarreiraFisica');
            if ($solicitacao->save())
            {
                return [
                    'status' => true,
                    'solicitacao' => $solicitacao,
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'solicitacao' => $solicitacao,
                    'error' => print_r($solicitacao->getErrors(), true)
                ];
            }
        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
    }

    public function actionUpdateDistanciaEscolaAjax($idSolicitacao)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $solicitacao = SolicitacaoTransporte::findOne($idSolicitacao);

            if (!$solicitacao) {
                return [
                    'status' => false
                ];
            }
            
            $solicitacao->distanciaEscola = Yii::$app->request->post('distanciaEscola');
            if ($solicitacao->save())
            {
                return [
                    'status' => true,
                    'solicitacao' => $solicitacao,
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'solicitacao' => $solicitacao,
                    'error' => print_r($solicitacao->getErrors(), true)
                ];
            }
        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
    }

    public function actionUpdateCartaoValeTransporteAjax($idSolicitacao)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $solicitacao = SolicitacaoTransporte::findOne($idSolicitacao);

            if (!$solicitacao) {
                return [
                    'status' => false
                ];
            }
            
            $solicitacao->cartaoValeTransporte = Yii::$app->request->post('cartaoValeTransporte');
            if ($solicitacao->save())
            {
                return [
                    'status' => true,
                    'solicitacao' => $solicitacao,
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'solicitacao' => $solicitacao,
                    'error' => print_r($solicitacao->getErrors(), true)
                ];
            }
        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
    }

    public function actionUpdateCartaoPasseEscolarAjax($idSolicitacao)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $solicitacao = SolicitacaoTransporte::findOne($idSolicitacao);

            if (!$solicitacao) {
                return [
                    'status' => false
                ];
            }
            
            $solicitacao->cartaoPasseEscolar = Yii::$app->request->post('cartaoPasseEscolar');
            if ($solicitacao->save())
            {
                return [
                    'status' => true,
                    'solicitacao' => $solicitacao,
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'solicitacao' => $solicitacao,
                    'error' => print_r($solicitacao->getErrors(), true)
                ];
            }

        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
    }

     public function actionRenovacoes()
    {
        // $anoCorrente = date("Y") - 1;
        // $anoVigente = $this->configuracao->calcularAno();
        // $dataVigente = explode('-',$configuracao->dataVigente);


        // $searchModel = new SolicitacaoTransporteSearch();
        // $searchModel->status = SolicitacaoTransporte::STATUS_ATENDIDO;
        // $searchModel->anoVigente = $anoCorrente;
        // $searchModel->tipoSolicitacao = SolicitacaoTransporte::SOLICITACAO_BENEFICIO;

        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        // $dataProvider->query->andWhere(['<>','solicitacaoTransporte.tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO]);
        // $dataProvider->query->andWhere(['solicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_ATENDIDO]);
        // $dataProvider->query->andWhere(['solicitacaoTransporte.anoVigente' => $anoCorrente]);


        // cast list object in dataprovider
        // $dataProvider->query->join('aluno')->andFilterWhere(['status'=>1]); 
        // return $this->render('renovacoes', [
        //   'searchModel' => $searchModel,
        //   'dataProvider' => $dataProvider,
        // ]);



        $idsPasse = [];
        $SolicitacoesPasseEscolar = SolicitacaoTransporte::find()
            ->select('id')
            ->andWhere(['<>', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
            ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_DEFERIDO])
            ->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE])
            // ->andWhere(['>=', 'SolicitacaoTransporte.data',$anoCorrente.'-01-01'])
            ->andWhere(['<', 'SolicitacaoTransporte.data', $this->configuracao->dataVigente])
            ->all();
        // ->andWhere(['<=','SolicitacaoTransporte.anoVigente', $anoVigente])
        $idsPasse = array_column($SolicitacoesPasseEscolar, 'id');
        // print_r($idsPasse);
        $solicitacoesCorrentes = SolicitacaoTransporte::find()
            ->andWhere(['<>', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
            //->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_ATENDIDO])
            //->orWhere(['in', 'SolicitacaoTransporte.id', $idsPasse])
            // ->andWhere(['>=', 'SolicitacaoTransporte.data',$anoCorrente.'-01-01'])
            ->andWhere(['<', 'SolicitacaoTransporte.data', $this->configuracao->dataVigente])
            // ->andWhere(['<=','SolicitacaoTransporte.anoVigente', $anoVigente])
            ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
            ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno');



        $st = $solicitacoesCorrentes;
        if ($get = Yii::$app->request->get()) {
           if($get['tipoFrete'] == 3){
				$st->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => 2]);
			}else{
				if ($get['tipoFrete']) {
					$st->andWhere(['SolicitacaoTransporte.tipoFrete' => $get['tipoFrete']]);
				}	
			}            
            if ($get['unidade']){
				$st->andWhere(['Escola.unidade' => $get['unidade']]);
			} 
            if ($get['escola']){
				 $st->andWhere(['SolicitacaoTransporte.idEscola' => $get['escola']]);
			}
            if ($get['ano']){
				$st->andWhere(['SolicitacaoTransporte.anoVigente' => $get['ano']]);
			} 
            if ($get['regiao']){
				$st->andWhere(['Escola.regiao' => $get['regiao']]);
			} 
			if ($get['motivoNaoRenova']){
				$st->andWhere(['SolicitacaoTransporte.motivoNaoRenova' => $get['motivoNaoRenova']]);
			} 
				
            if ($get['tipoDeSolicitacao']){
				$st->andWhere(['SolicitacaoTransporte.novaSolicitacao' => $get['tipoDeSolicitacao']]);
			} 
        }


        return $this->render('renovacoes', [
            'solicitacoesTransporte' => $st->all(),
            //'tiposFrete' => Aluno::ARRAY_MODALIDADE,
            'unidades' => Escola::ARRAY_UNIDADE,
            'escolas' => ArrayHelper::map(Escola::find()->all(), 'id', 'nomeCompleto'),
            'regioes' => Escola::ARRAY_REGIAO
        ]);
    }

    public function actionEncerrarSolicitacao($id)
    {
        $solicitacaoAntiga = SolicitacaoTransporte::findOne($id);
        PontoAluno::removerTodasRotas($solicitacaoAntiga->idAluno);
        $solicitacaoAntiga->status = SolicitacaoTransporte::STATUS_ENCERRADA;
        $solicitacaoAntiga->ultimaMovimentacao = date('Y-m-d');
        $solicitacaoAntiga->save();
        $modelStatus = new SolicitacaoStatus();
        $modelStatus->idUsuario = \Yii::$app->User->identity->id;
        $modelStatus->dataCadastro = date('Y-m-d');
        $modelStatus->status = SolicitacaoTransporte::STATUS_ENCERRADA;
        $modelStatus->idSolicitacaoTransporte = $solicitacaoAntiga->id;
        $modelStatus->justificativa = 'ENCERRADO POR FALTA DE RENOVAÇÃO';
        $modelStatus->save();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => true];
    }

    // Encerra todas as solicitações que não foram renovadas
    public function actionEncerrarTodas()
    {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $anoCorrente = date("Y") - 1;
        $data = [];

        $solicitacoesCorrentes = SolicitacaoTransporte::find()
            // ->andWhere(['<>','SolicitacaoTransporte.tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
            // ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_ATENDIDO])
            // ->andWhere(['>=', 'SolicitacaoTransporte.data',$anoCorrente.'-01-01'])
            ->andWhere(['<', 'SolicitacaoTransporte.data', $this->configuracao->dataVigente])
            // ->andWhere(['<=','SolicitacaoTransporte.anoVigente', $anoVigente])
            ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
            ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno')
            ->all();

        // print_r($solicitacoesCorrentes);
        foreach ($solicitacoesCorrentes as $solicitacaoAntiga) {
            // Criação em massa de status
            $solicitacaoAntiga->status =  SolicitacaoTransporte::STATUS_ENCERRADA;
            $solicitacaoAntiga->save();
            PontoAluno::removerTodasRotas($solicitacaoAntiga->idAluno);
            $data[] = [\Yii::$app->User->identity->id,  date('Y-m-d'), SolicitacaoTransporte::STATUS_ENCERRADA, $solicitacaoAntiga->id, 'ENCERRADO POR FALTA DE RENOVAÇÃO'];
        }


        Yii::$app->db
            ->createCommand()
            ->batchInsert('SolicitacaoStatus', ['idUsuario', 'dataCadastro', 'status', 'idSolicitacaoTransporte', 'justificativa'], $data)
            ->execute();
        return $this->redirect(['renovacoes']);
    }
    /**
     * Finds the SolicitacaoTransporte model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SolicitacaoTransporte the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SolicitacaoTransporte::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function searchPeriodo($periodo, $model)
    {
        $data = explode('- ', $periodo);

        $data[1] = explode('/', $data[1]);
        $data[1] = $data[1][2] . '-' . $data[1][1] . '-' . $data[1][0];
        $data[0] = explode('/', $data[0]);
        $data[0] = trim($data[0][2]) . '-' . $data[0][1] . '-' . $data[0][0];

        $model->andWhere(['>=', 'data', $data[0]])
            ->andWhere(['<=', 'data', $data[1]]);

        return $model;
    }

    public function actionExcluirPorAdmin($id)
    {
        $solicitacaoAntiga = SolicitacaoTransporte::findOne($id);
        $idSol = $solicitacaoAntiga->id;
        $idAluno = $solicitacaoAntiga->idAluno;
        PontoAluno::removerTodasRotas($solicitacaoAntiga->idAluno);
        HistoricoMovimentacaoRota::deleteAll(['idSolicitacaoTransporte' => $idSol]);
        $solicitacaoAntiga->delete();

        return $this->redirect(['aluno/view', 'id' => $idAluno]);
    }
}
