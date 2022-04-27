<?php

namespace frontend\controllers;

use Yii;
use common\models\CondutorRota;
use common\models\CondutorRotaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Escola;
use common\models\Aluno;
use common\models\Ponto;
use common\models\PontoAluno;
use common\models\Log;

use common\models\Condutor;
use common\models\PontoEscola;
use common\models\SolicitacaoTransporte;
use common\models\SolicitacaoStatus;
use yii\helpers\BaseHtml;
use common\models\HistoricoMovimentacaoRota;
use kartik\mpdf\Pdf;
use kartik\export\ExportMenu;

/**
 * CondutorRotaController implements the CRUD actions for CondutorRota model.
 */
class CondutorRotaController extends Controller
{
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
        ];
    }

    public function actionSearchEscolas()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //$post = json_decode(file_get_contents('php://input'), true);
        $escolas = Escola::find();
        if (isset($_POST['escolas']))
            $escolas->where(['not in', 'id', $_POST['escolas']]);
        return $escolas->all();
        //return $post;
    }

    public function actionSearchAlunos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $rota = CondutorRota::findOne($_POST['idCondutorRota']);
        $condutorSelecionado = Condutor::findOne($_POST['idCondutor']);
        // Pega todas as escolas atribuídas a um condutor
        $escolasDisponiveis = [];
        $escolasDisponiveis[] = 0;
        foreach($condutorSelecionado->escolas as $escola) {
            $escolasDisponiveis[] = $escola->idEscola;
        }
        $solicitacoes = SolicitacaoTransporte::find()
            ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_DEFERIDO])
            ->andWhere(['modalidadeBeneficio' => Aluno::MODALIDADE_FRETE])
            ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO]);

        // if (isset($_POST['alunos'])) {
        //     $solicitacoes->andWhere(['not in', 'idAluno', $_POST['alunos']]);
        // }
        if($escolasDisponiveis) {
            $solicitacoes->andWhere(['in', 'idEscola', $escolasDisponiveis ]);
        }
        $solicitacoes = $solicitacoes->all();
       
        $alunos = [];
        
		
        if (isset($_POST['alunosBanco']) && $_POST['alunosBanco'] != '') {
            $alunosBanco = Aluno::find()->where(['in', 'id', $_POST['alunosBanco']])->all();
            foreach ($alunosBanco as $alunoBanco) {
                // if(in_array($alunoBanco->idEscola, $escolasDisponiveis)){					
                    $alunos[] = $alunoBanco;
                // }
            }
        }
		
        foreach ($solicitacoes as $solicitacao) {
            // Evita duplicidade no arr e verifica se NÃO está em nenhum ponto
            // mais abaixo temos um PontoAluno::find para tratar alunos que já estão em uma rota oposta
            if (!in_array($solicitacao->aluno, $alunos) && !$aluno->alunoPonto)	

				if($solicitacao->aluno->turno == 4){
					$alunos[] = $solicitacao->aluno;
				}else{
					if($solicitacao->aluno->turno == $rota['turno']){
						$alunos[] = $solicitacao->aluno;
					}                
				}
				
        }


        //Mecanismo para pegar alunos que estão em rotas de sentido OPOSTO
        
        $cc = PontoAluno::find()
            ->select('idAluno, COUNT(id) as contagem, sentido')
            ->groupBy('idAluno')
            ->having(['<', 'contagem', 2])
            ->all();

        // $alunos = [];
        foreach ($cc as $ponto) {
            // print $ponto->idAluno.'<br><br>';
            if ($ponto->sentido != $rota->sentido) {
                $aluno = Aluno::findOne($ponto->idAluno);
                // print_r($aluno);
                if (!in_array($aluno, $alunos) && in_array($aluno->idEscola, $escolasDisponiveis))
                    $alunos[] = $aluno;
            }
        }
        return $alunos;

    }
    // public function actionSearchAlunos()
    // {
    //     Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //     $rota = CondutorRota::findOne($_POST['idCondutorRota']);
    //     $condutorSelecionado = Condutor::findOne($_POST['idCondutor']);
    //     // Pega todas as escolas atribuídas a um condutor
    //     $escolasDisponiveis = [];
    //          $escolasDisponiveis[] = 0;
    //     foreach($condutorSelecionado->escolas as $escola) {
    //         $escolasDisponiveis[] = $escola->idEscola;
    //     }
    //     $solicitacoes = SolicitacaoTransporte::find()
    //         ->andwhere(['in', 'SolicitacaoTransporte.status', [SolicitacaoTransporte::STATUS_DEFERIDO, SolicitacaoTransporte::STATUS_ATENDIDO]])
    //         ->andWhere(['modalidadeBeneficio' => Aluno::MODALIDADE_FRETE])
    //         ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO]);
        

    //     if($escolasDisponiveis) {
    //         $solicitacoes->andWhere(['in', 'idEscola', $escolasDisponiveis ]);
    //     }

    //     $solicitacoes = $solicitacoes->all();
    //     // print_r($solicitacoes);
    //     $alunos = [];
            


    //     if (isset($_POST['alunosBanco']) && $_POST['alunosBanco'] != '') {
    //         $alunosBanco = Aluno::find()->where(['in', 'id', $_POST['alunosBanco']])->all();
    //         foreach ($alunosBanco as $alunoBanco) {
    //             $alunos[] = $alunoBanco;
    //         }
    //     }
    //     foreach ($solicitacoes as $solicitacao) {
    //         // Evita duplicidade no arr e verifica se NÃO está em nenhum ponto
    //         // mais abaixo temos um PontoAluno::find para tratar alunos que já estão em uma rota oposta
    //         if (!in_array($solicitacao->aluno, $alunos) && !$aluno->alunoPonto)
    //             $alunos[] = $solicitacao->aluno;
    //     }


    //     //Mecanismo para pegar alunos que estão em rotas de sentido OPOSTO
    //     $cc = PontoAluno::find()
    //         ->select('idAluno, COUNT(id) as contagem, sentido')
    //         ->having(['<', 'contagem', 2])
    //         ->all();
    //     // print_r($alunos);
    //     // $alunos = [];
    //     foreach ($cc as $ponto) {
    //         // print $ponto->idAluno.'<br><br>';
    //         if ($ponto->sentido != $rota->sentido) {
    //             $aluno = Aluno::findOne($ponto->idAluno);
    //             // print_r($aluno);
    //             if (!in_array($aluno, $alunos))
    //                 $alunos[] = $aluno;
    //         }
    //     }
    //     return $alunos;

    // }

    /**
     * Lists all CondutorRota models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new CondutorRotaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = ['pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20];
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        Yii::$app->session->set('rotas', $dataProvider->getModels());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReport()
    {
        $contentBefore = '';
        $dados = Yii::$app->session->get('rotas');

        // print_r($dados);
        // // $this->sort($dados);
        // if (!$dados)
        //     $dados = [];

        $content = '';
        $content .= '<table border="0" width="100%" class="table">';
        $content .= '
        
      <tr>
        <th align="center"><b>Cod.</b></th>
        <th align="center"><b>Condutor</b></th>
        <th align="center"><b>Turno</b></th>
        <th align="center"><b>Viagem</b></th>
        <th align="center"><b>Descrição</b></th>
        <th align="center"><b>Sentido</b></th>
        <th align="center"><b>Escola(s) na Viagem</b></th>
        <th align="center"><b>Capacidade do veículo</b></th>
        <th align="center"><b>Assentos livres</b></th>
      </tr>';

        foreach ($dados as $model) {
            $content .= '<tr>';
            $content .= $this->td(5, $model->id);
            $content .= $this->td(20, $model->condutor ? $model->condutor->nome : '-');
            $content .= $this->tdCenter(7, $model->turno ? CondutorRota::ARRAY_TURNOS[$model->turno] : '-');
            $content .= $this->tdCenter(20, $model->viagem ? CondutorRota::ARRAY_VIAGEM[$model->viagem] : '-');
            $content .= $this->tdCenter(30, $model->descricao);
            $content .= $this->tdCenter(7, $model->sentido ? CondutorRota::ARRAY_SENTIDO[$model->sentido] : '-');

            $escolas = '<ul>';
            foreach ($model->escolaPonto as $escola)
            {
                $escolas .= '<li>'.$escola->escola->nome.'</li>';
            }
            $escolas .= '</ul>';

            $content .= $this->td(20, $escolas);

            $content .= $this->tdCenter(7, $model->condutor ? $model->condutor->veiculo->capacidade :  '-');
            $content .= $this->tdCenter(7, $model->condutor ? $model->condutor->veiculo->capacidade - count($model->alunoPonto) : '-');
            $content .= '</tr>';
        }
        $content .= '</table>';

        $pdf = new Pdf([
            'mode' => 'c',
            'marginTop' => 50,
            'marginBottom' => 20,
            'marginLeft' => 5,
            'marginRight' => 5,
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .table table { border-collapse: collapse; } .table table, .table th, .table td { border: 1px solid black;} .table th td { padding-left: 3px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['
                <table width="100%">
                <tr>
                  <Td align="center">
                  <img src="img/brasaoFull.png">
                  </Td>
                </tr>
              </table>'],
                'SetFooter' => ['Emitido em ' . date('d/m/Y') . '|| {PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Displays a single CondutorRota model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CondutorRota model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new CondutorRota();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['roterizar', 'idCondutorRota' => $model->id]);
        } else {
			
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    // private function salvarMovimentacaoRemocao($sol, $rota) {
    //     $this->salvarMovimentacao([
    //         'tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_REMOVIDO,
    //         'idCondutorRotaAnterior' => $rota->id,
    //         'idCondutorAnterior' => $rota->idCondutor,
    //         'idVeiculoAnterior' => $rota->condutor->idVeiculo,
    //         'idSolicitacaoTransporte' => $sol->id,
    //         'idAluno' => $sol->idAluno,
    //         'idEscola' => $sol->idEscola,
    //         'idUsuario' => \Yii::$app->User->identity->id,
    //         'sentido' => $rota->sentido
    //     ]);
    // }
    private function salvarMovimentacaoInsercao($sol, $rota) {
        $this->salvarMovimentacao([
            'tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_INSERIDO,
            'idCondutorRotaAtual' => $rota->id,
            'idCondutorAtual' => $rota->idCondutor,
            'idVeiculoAtual' => $rota->condutor->idVeiculo,
            'idSolicitacaoTransporte' => $sol->id,
            'idAluno' => $sol->idAluno,
            'idEscola' => $sol->idEscola,
            'idUsuario' => \Yii::$app->User->identity->id,
            'sentido' => $rota->sentido
        ]);
    }
    private function salvarMovimentacao($dados) {
        HistoricoMovimentacaoRota::salvar($dados);
   
    }
    public function actionSalvarRota()
    {
		

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $rota = CondutorRota::findOne($_POST['idCondutorRota']);
        //Atualizar Condutor
        if (isset($_POST['idCondutor'])) {
            $idCondutor = $_POST['idCondutor'];
            if($rota->idCondutor != $idCondutor)
                $rota->oldIdCondutor = $rota->idCondutor;
            $rota->idCondutor = $idCondutor;
			$rota->save();
        }
			

        


        $logs = [];
        $logAlunos = [];
        $logAdicionado = [];
        $logRemovido = [];
        if(isset($_POST['logs'])){
            $logs = $_POST['logs'];
            foreach($logs as $log){
                $logAlunos[] = $log['ponto'];
                if($log['log']=='REMOVIDO')
                    $logRemovido[] = $log['ponto'];
                else 
                    $logAdicionado[] = $log['ponto'];
            }
        }   
        
		
		foreach($logRemovido as $val){
			$sqlSol ='select * from SolicitacaoTransporte st where st.idAluno = '.$val.' and st.`status` ='.SolicitacaoTransporte::STATUS_ATENDIDO ;
			$dadosSol = Yii::$app->getDb()->createCommand($sqlSol)->queryAll();			
			if($dadosSol[0]['id']){
				$solicitacao = SolicitacaoTransporte::findOne($dadosSol[0]['id']);
				$solicitacao->status = SolicitacaoTransporte::STATUS_DEFERIDO;    
				$solicitacao->save();
				
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_DEFERIDO;
				$modelStatus->idSolicitacaoTransporte = $dadosSol[0]['id'];
				$modelStatus->justificativa = 'REMOVIDO DA ROTA DO CONDUTOR. RETORNADO PARA RECEBIDO';
				$modelStatus->save();
				
			}			
		}
		
			
        //DESATIVAR BENEFICIO DE ALUNOS RELACIONADOS A ESSA ROTA
        $pontos = Ponto::find()->where(['idCondutorRota' => $rota->id])->all();
		
        // $pontosIds = array_column($pontos, 'id');
        // $pontoAlunos = PontoAluno::find()->where(['in', 'idPonto', $pontosIds])->all();
        // $pontoAlunosIds = array_column($pontoAlunos, 'id');
        // for ($i = 0; $i < count($_POST['pontos']); $i++) {
        //     $pontoPost = $_POST['pontos'][$i];
        //     $novoPonto = [];
        //     $novoPonto = $_POST['pontos'][$i];
        //     unset($novoPonto['pontos'][$i]['alunos']);
        //     if (isset($pontoPost['alunos'])) {
        //         $indexAluno = 0;
        //         foreach ($pontoPost['alunos'] as $idAluno) {
        //             if(!in_array($idAluno,$pontoAlunosIds)){
        //                $novoPonto['alunos'][$indexAluno] = $idAluno; 
        //             }
        //             $indexAluno++;
        //         }        
        //     }
        //     $_POST['pontos'][$i] = $novoPonto;
        // }
        // return $_POST;            
        // exit(1);
        foreach ($pontos as $ponto) {
            //print '1 ';
            //1638 1639 1640 
            // print $ponto->id.' ';
            // print_r($ponto->pontoAlunos);
            foreach ($ponto->pontoAlunos as $aluno) {
                // print 'tentar retornar deferido'.$aluno->idAluno.' <br>';
                SolicitacaoTransporte::retornarDeferido($aluno->idAluno, $rota, $logRemovido);
				
				\Yii::$app->db->createCommand("UPDATE Aluno	SET cienteCondutor=:cienteCondutor 	WHERE id=".$aluno->idAluno)->bindValue(':cienteCondutor', 0)->execute();				
				
            }

        }
        // exit(1);
        $sols = SolicitacaoTransporte::find()->where(['idRotaIda' => $rota->id])->orWhere(['idRotaVolta' => $rota->id])->all();
		
        foreach($sols as $sol) {
            if($sol->idRotaIda == $rota->id){
                SolicitacaoTransporte::logRotaIda($sol,$rota);
                // $this->salvarMovimentacaoRemocao($sol, $rota);
                $sol->idRotaIda = null;
                $sol->save();
                $log = new Log();
                $log->data = date('Y-m-d H:i:s');
                $log->acao = Log::ACAO_ATUALIZAR;
                $log->referencia = $sol->id;
                $log->tabela =  'SolicitacaoTransporte';
                $log->coluna = 'idRotaIda';
                $log->antes =  $rota->id;
                $log->depois = '';
                $log->idUsuario = \Yii::$app->User->identity->id;
                $log->idSolicitacaoTransporteTable = $sol->id;
                $log->idCondutorRotaTable = $rota->id;
                $log->idAlunoTable = $sol->idAluno;
                $log->save();
            }
                
            if($sol->idRotaVolta == $rota->id){
                SolicitacaoTransporte::logRotaVolta($sol,$rota);
                // $this->salvarMovimentacaoRemocao($sol, $rota);
                $sol->idRotaVolta = null;
                $sol->save();
                $log = new Log();
                $log->data = date('Y-m-d H:i:s');
                $log->acao = Log::ACAO_ATUALIZAR;
                $log->referencia = $sol->id;
                $log->tabela =  'SolicitacaoTransporte';
                $log->coluna = 'idRotaVolta';
                $log->antes =  $rota->id;
                $log->depois = '';
                $log->idUsuario = \Yii::$app->User->identity->id;
                $log->idSolicitacaoTransporteTable = $sol->id;
                $log->idCondutorRotaTable = $rota->id;
                $log->idAlunoTable = $sol->idAluno;
                $log->save();
            }

        }
    // SolicitacaoTransporte::updateAll(['idRotaIda' => null], ['idRotaIda' => $rota->id] );
        // SolicitacaoTransporte::updateAll(['idRotaVolta' => null], ['idRotaVolta' => $rota->id] );
        Ponto::deleteAll(['idCondutorRota' => $rota->id, 'sentido' => $rota->sentido]);
        for ($i = 0; $i < count($_POST['pontos']); $i++) {
            $pontoPost = $_POST['pontos'][$i];

            $ponto = new Ponto();
            $ponto->tipo = $pontoPost['tipo'];
            $ponto->lat = $pontoPost['lat'];
            $ponto->lng = $pontoPost['lng'];
            $ponto->idCondutorRota = $rota->id;
            $ponto->sentido = $rota->sentido;
            $ponto->save();

            if (isset($pontoPost['alunos'])) {
                foreach ($pontoPost['alunos'] as $idAluno) {
                    SolicitacaoTransporte::retornarAtendido($idAluno, $rota, $logAdicionado);
                    $pontoAluno = new PontoAluno();
                    $pontoAluno->idPonto = $ponto->id;
                    $pontoAluno->idAluno = $idAluno;
                    $pontoAluno->sentido = $rota->sentido;
                    
                    if(!$pontoAluno->save()){
                        $ponto->delete();
                    }
                    
                    
                    $solicitacao = SolicitacaoTransporte::find()
                    ->where(['=', 'status', SolicitacaoTransporte::STATUS_ATENDIDO])
                    ->andWhere(['=', 'idAluno', $idAluno])
                    ->one();
                    if($rota->sentido == CondutorRota::SENTIDO_IDA) {
                        $solicitacao->idRotaIda = $rota->id;
                        $this->salvarMovimentacaoInsercao($solicitacao, $rota, $logAdicionado);
                        $log = new Log();
                        $log->data = date('Y-m-d H:i:s');
                        $log->acao = Log::ACAO_ATUALIZAR;
                        $log->referencia = $solicitacao->id;
                        $log->tabela =  'SolicitacaoTransporte';
                        $log->coluna = 'idRotaIda';
                        $log->antes =  '';
                        $log->depois = $rota->id;
                        $log->idUsuario = \Yii::$app->User->identity->id;
                        $log->idSolicitacaoTransporteTable = $solicitacao->id;
                        $log->idCondutorRotaTable = $rota->id;
                        $log->idAlunoTable = $solicitacao->idAluno;
                        $log->save();

                    } else {
                        $solicitacao->idRotaVolta = $rota->id;
                        $this->salvarMovimentacaoInsercao($solicitacao, $rota);

                        $log = new Log();
                        $log->data = date('Y-m-d H:i:s');
                        $log->acao = Log::ACAO_ATUALIZAR;
                        $log->referencia = $solicitacao->id;
                        $log->tabela =  'SolicitacaoTransporte';
                        $log->coluna = 'idRotaVolta';
                        $log->antes =  '';
                        $log->depois = $rota->id;
                        $log->idUsuario = \Yii::$app->User->identity->id;
                        $log->idSolicitacaoTransporteTable = $solicitacao->id;
                        $log->idCondutorRotaTable = $rota->id;
                        $log->idAlunoTable = $solicitacao->idAluno;
                        $log->save();

                    }
                    if ($rota->idCondutor) {
                        $solicitacao->idCondutor = $rota->idCondutor;
                    }
                    $solicitacao->save(false);
                }
            }
            if (isset($pontoPost['escolas'])) {
                foreach ($pontoPost['escolas'] as $idEscola) {
                    $pontoEscola = new PontoEscola();
                    $pontoEscola->idPonto = $ponto->id;
                    $pontoEscola->idEscola = $idEscola;
                    $pontoEscola->sentido = $rota->sentido;
                    $pontoEscola->save();
                }
            }
        }
		

        return ['status' => true, 'adicionados' => $logAdicionado, 'removidos' => $logRemovido];
    }


    // public function actionSalvarRota()
    // {
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //     $rota = CondutorRota::findOne($_POST['idCondutorRota']);
    //     //Atualizar Condutor
    //     if (isset($_POST['idCondutor'])) {
    //         $rota->idCondutor = $_POST['idCondutor'];
    //     }

    //     $rota->save();

    //     //DESATIVAR BENEFICIO DE ALUNOS RELACIONADOS A ESSA ROTA
    //     $pontos = Ponto::find()->where(['idCondutorRota' => $rota->id])->all();
        
    //     foreach ($pontos as $ponto) {
    //         //print '1 ';
    //         //1638 1639 1640 
    //         // print $ponto->id.' ';
    //         // print_r($ponto->pontoAlunos);
    //         foreach ($ponto->pontoAlunos as $aluno) {
    //             print $aluno.'xxx';
    //             // print 'tentar retornar deferido'.$aluno->idAluno.' <br>';
    //             SolicitacaoTransporte::retornarDeferido($aluno->idAluno, $rota);
    //             // $idSolicitacao = $aluno->solicitacao->id;
    //             // $solicitacao = SolicitacaoTransporte::findOne($idSolicitacao);
    //             // if($solicitacao->status == SolicitacaoTransporte::STATUS_ATENDIDO){
    //             //     $solicitacao->status = SolicitacaoTransporte::STATUS_DEFERIDO;
    //             //     $solicitacao->save();
    //             // }
    //         }
    //     }
    //     // exit(1);
    //     $sols = SolicitacaoTransporte::find()->where(['idRotaIda' => $rota->id])->orWhere(['idRotaVolta' => $rota->id])->all();
    //     foreach($sols as $sol) {
    //         if($sol->idRotaIda == $rota->id){
    //             SolicitacaoTransporte::logRotaIda($sol);
    //             // $this->salvarMovimentacaoRemocao($sol, $rota);
    //             $sol->idRotaIda = null;
    //             $sol->save();
    //             $log = new Log();
    //             $log->data = date('Y-m-d H:i:s');
    //             $log->acao = Log::ACAO_ATUALIZAR;
    //             $log->referencia = $sol->id;
    //             $log->tabela =  'SolicitacaoTransporte';
    //             $log->coluna = 'idRotaIda';
    //             $log->antes =  $rota->id;
    //             $log->depois = '';
    //             $log->idUsuario = \Yii::$app->User->identity->id;
    //             $log->idSolicitacaoTransporteTable = $sol->id;
    //             $log->idCondutorRotaTable = $rota->id;
    //             $log->idAlunoTable = $sol->idAluno;
    //             $log->save();
    //         }
                
    //         if($sol->idRotaVolta == $rota->id){
    //             SolicitacaoTransporte::logRotaVolta($sol);
    //             // $this->salvarMovimentacaoRemocao($sol, $rota);
    //             $sol->idRotaVolta = null;
    //             $sol->save();
    //             $log = new Log();
    //             $log->data = date('Y-m-d H:i:s');
    //             $log->acao = Log::ACAO_ATUALIZAR;
    //             $log->referencia = $sol->id;
    //             $log->tabela =  'SolicitacaoTransporte';
    //             $log->coluna = 'idRotaVolta';
    //             $log->antes =  $rota->id;
    //             $log->depois = '';
    //             $log->idUsuario = \Yii::$app->User->identity->id;
    //             $log->idSolicitacaoTransporteTable = $sol->id;
    //             $log->idCondutorRotaTable = $rota->id;
    //             $log->idAlunoTable = $sol->idAluno;
    //             $log->save();
    //         }

    //     }
    // // SolicitacaoTransporte::updateAll(['idRotaIda' => null], ['idRotaIda' => $rota->id] );
    //     // SolicitacaoTransporte::updateAll(['idRotaVolta' => null], ['idRotaVolta' => $rota->id] );
    //     Ponto::deleteAll(['idCondutorRota' => $rota->id, 'sentido' => $rota->sentido]);
    //     for ($i = 0; $i < count($_POST['pontos']); $i++) {
    //         $pontoPost = $_POST['pontos'][$i];

    //         $ponto = new Ponto();
    //         $ponto->tipo = $pontoPost['tipo'];
    //         $ponto->lat = $pontoPost['lat'];
    //         $ponto->lng = $pontoPost['lng'];
    //         $ponto->idCondutorRota = $rota->id;
    //         $ponto->sentido = $rota->sentido;
    //         $ponto->save();

    //         if (isset($pontoPost['alunos'])) {
    //             foreach ($pontoPost['alunos'] as $idAluno) {
    //                 // print $idAluno;
    //                 SolicitacaoTransporte::retornarAtendido($idAluno, $rota);
    //                 $pontoAluno = new PontoAluno();
    //                 $pontoAluno->idPonto = $ponto->id;
    //                 $pontoAluno->idAluno = $idAluno;
    //                 $pontoAluno->sentido = $rota->sentido;
                    
    //                 if(!$pontoAluno->save()){
    //                     $ponto->delete();
    //                 }
                    
                    
    //                 $solicitacao = SolicitacaoTransporte::find()
    //                 ->where(['=', 'status', SolicitacaoTransporte::STATUS_ATENDIDO])
    //                 ->andWhere(['=', 'idAluno', $idAluno])
    //                 ->one();
    //                 // print 'x';
    //                 // print_r($solicitacao);
    //                 exit(1);
    //                 if($rota->sentido == CondutorRota::SENTIDO_IDA) {
    //                     $solicitacao->idRotaIda = $rota->id;
    //                     $this->salvarMovimentacaoInsercao($solicitacao, $rota);
    //                     $log = new Log();
    //                     $log->data = date('Y-m-d H:i:s');
    //                     $log->acao = Log::ACAO_ATUALIZAR;
    //                     $log->referencia = $solicitacao->id;
    //                     $log->tabela =  'SolicitacaoTransporte';
    //                     $log->coluna = 'idRotaIda';
    //                     $log->antes =  '';
    //                     $log->depois = $rota->id;
    //                     $log->idUsuario = \Yii::$app->User->identity->id;
    //                     $log->idSolicitacaoTransporteTable = $solicitacao->id;
    //                     $log->idCondutorRotaTable = $rota->id;
    //                     $log->idAlunoTable = $solicitacao->idAluno;
    //                     $log->save();

    //                 } else {
    //                     $solicitacao->idRotaVolta = $rota->id;
    //                     $this->salvarMovimentacaoInsercao($solicitacao, $rota);

    //                     $log = new Log();
    //                     $log->data = date('Y-m-d H:i:s');
    //                     $log->acao = Log::ACAO_ATUALIZAR;
    //                     $log->referencia = $solicitacao->id;
    //                     $log->tabela =  'SolicitacaoTransporte';
    //                     $log->coluna = 'idRotaVolta';
    //                     $log->antes =  '';
    //                     $log->depois = $rota->id;
    //                     $log->idUsuario = \Yii::$app->User->identity->id;
    //                     $log->idSolicitacaoTransporteTable = $solicitacao->id;
    //                     $log->idCondutorRotaTable = $rota->id;
    //                     $log->idAlunoTable = $solicitacao->idAluno;
    //                     $log->save();

    //                 }
    //                 if ($rota->idCondutor) {
    //                     $solicitacao->idCondutor = $rota->idCondutor;
    //                 }
    //                 $solicitacao->save(false);
    //             }
    //         }
    //         if (isset($pontoPost['escolas'])) {
    //             foreach ($pontoPost['escolas'] as $idEscola) {
    //                 $pontoEscola = new PontoEscola();
    //                 $pontoEscola->idPonto = $ponto->id;
    //                 $pontoEscola->idEscola = $idEscola;
    //                 $pontoEscola->sentido = $rota->sentido;
    //                 $pontoEscola->save();
    //             }
    //         }
    //     }

    //     return ['status' => true];
    // }
    public function actionCreateAjax()
    {
		
        $model = new CondutorRota();
        if (Yii::$app->request->get('idCondutor')) {
            $model->idCondutor = Yii::$app->request->get('idCondutor');
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['status' => true];
        } else {

            return $this->renderAjax('_formAjax', [
                'model' => $model,
                'action' => 'condutor-rota/create-ajax',
            ]);
        }
    }

    public function actionRoterizar($idCondutorRota)
    {
        $model = CondutorRota::findOne($idCondutorRota);
		$dadosCondutor = Condutor::find()->andWhere(['id' => $model->idCondutor])->one();
		
        return $this->render('roterizar', [
            'model' => $model,
			'statusCondutor' => $dadosCondutor->status,

        ]);
    }
    private function castArr($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->castArr($value);
            }
            return $result;
        }
        return $data;
    }


    /**
     * Este método é responsável por retornar os pontos de uma rota
     * ELe é utilizado na roteirização do tesc
     *
     * @property integer $idCondutorRota id da rota que queremos os pontos
     * @property integer $idCondutor quando não enviado, o método trabalha com os dados 
     *                              do condutor corrente, quando passado o sistema realiza
     *                              uma verificação de escolas atribuídas a este condutor passado
     */
    public function actionViewAjax($idCondutorRota, $idCondutor=false)
    {
        // Existe com único propósito de facilitar o inspect no network do chrome
        $flagNovoCondutor = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $rota = CondutorRota::findOne($idCondutorRota)->toArray();
        
        $rotaPontosValidos = [
                // 'pontos' => []
        ];

        // Caso seja um NOVO condutor, vamos obter a lista de escolas pertecentes à ele
        $escolasCondutor = [];
        $escolasCondutor[] = 0;
        if($idCondutor){
            $flagNovoCondutor = true;
            $condutorAtual = Condutor::findOne($idCondutor);
            foreach($condutorAtual->escolas as $escola) {
                $escolasCondutor[] = $escola->idEscola;
            }

            for ($i = 0; $i < count($rota['pontos']); $i++) {
                // print '$i='.$i;
                $alunos = [];
                for ($j = 0; $j < count($rota['pontos'][$i]['alunos']); $j++) {
                    // print '$j='.$j;

                    $escolaPonto = $rota['pontos'][$i]['alunos'][$j]['idEscola'];
                    // print  $rota['pontos'][$i]['alunos'][$j]['nome'].' ;;';
                    //count($escolasCondutor) > 0 &&
                    if(in_array($escolaPonto, $escolasCondutor)){
                        // unset($rota['pontos'][$i]['alunos'][$j]);
                        $alunos[] = $rota['pontos'][$i]['alunos'][$j];
                    }
                        
                }
                // if($alunos)
                     $rota['pontos'][$i]['alunos'] = $alunos;
                
            }

            for ($i = 0; $i <= count($rota['pontos']); $i++) {
                if(count($rota['pontos'][$i]['alunos']) > 0) {
                    $rota['pontos'][$i]['alunos'] = array_values($rota['pontos'][$i]['alunos']);
                    $rotaPontosValidos[] = $rota['pontos'][$i];
                }
            }
        }
        // print_r($rotaPontosValidos);
        // Caso o idCondutor (novo condutor) exista retornamos ele, senão retornamos o condutor
        // atual da rota
        return [
            'idCondutor' => $idCondutor ? $idCondutor : $rota['idCondutor'],
            'pontos' => $idCondutor ? $rotaPontosValidos : $rota['pontos'],
            'flagNovoCondutor' => $flagNovoCondutor,
        ];
    }
    /**
     * Updates an existing CondutorRota model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            foreach ($model->alunoPonto as $alunoPonto) {
                $solicitacao = SolicitacaoTransporte::find()
                    ->where(['=', 'status', SolicitacaoTransporte::STATUS_ATENDIDO])
                    ->andWhere(['=', 'idAluno', $alunoPonto->idAluno])
                    ->one();
                $solicitacao->idCondutor = $model->idCondutor;
                $solicitacao->save(false);
            }


            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CondutorRota model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $pontos = Ponto::find()->where(['idCondutorRota' => $id])->all();
        foreach ($pontos as $ponto) {
            foreach ($ponto->pontoAlunos as $aluno) {
                if($aluno){
                    \Yii::$app->getSession()->setFlash('error', 'Você não pode excluir uma rota com pontos nela');
                    return $this->redirect(['index']);
                }
            }
        }
        foreach ($pontos as $ponto) {
            foreach ($ponto->pontoAlunos as $aluno) {
                SolicitacaoTransporte::retornarDeferido($aluno->idAluno, $ponto->condutorRota);
            }
        }
        $model = $this->findModel($id);
        $model->rotaAtiva = 0;
        $model->save();
        // $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }



    /**
     * Finds the CondutorRota model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CondutorRota the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CondutorRota::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function tdCenter($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" align="center">' . $content . '</td>';
    }
    protected function td($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" >' . $content . '</td>';
    }
}
