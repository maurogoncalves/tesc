<?php

namespace frontend\controllers;

use common\models\Aluno;
use Yii;
use common\models\Veiculo;
use common\models\VeiculoSearch;
use common\models\TipoDocumento;
use yii\web\UploadedFile;
use common\models\DocumentoVeiculo;
use common\models\Condutor;
use common\models\CondutorRota;
use common\models\Escola;
use common\models\SolicitacaoTransporte;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\BaseHtml;
use yii\helpers\Html; 
use yii\filters\AccessControl;

/**
 * VeiculoController implements the CRUD actions for Veiculo model.
 */
class RotasController extends Controller
{
    public function actionView($id)
    {
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }   

    public function actionAlunosDisponiveis($id) {
        
        $alunosCadastrados = [];
        if(isset($_GET['alunosCadastrados']))
            $alunosCadastrados = explode(',',$_GET['alunosCadastrados']);
        
        
        $rota = $this->findModel($id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $escolasDisponiveis = [];
        foreach($rota->condutor->escolas as $escola) {
                $escolasDisponiveis[] = $escola->idEscola;
        }
        $solicitacoes = SolicitacaoTransporte::find()
        ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_DEFERIDO])
        ->andWhere(['modalidadeBeneficio' => Aluno::MODALIDADE_FRETE])
        ->andWhere(['tipoSolicitacao' => SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
        ->andWhere(['in', 'idEscola', $escolasDisponiveis ])
        ->andWhere(['not in', 'idAluno', $alunosCadastrados ])
        ->all();


        $output = [];
        $output['alunos'] = [];
        
        foreach($solicitacoes as $sol)
        {
            $possoAdd = true;
            foreach($sol->aluno->meusPontos as $ponto) {
                if($ponto->ponto->condutorRota->sentido == $rota->sentido) {
                    $possoAdd = false;
                }
            }
            if($possoAdd)
                $output['alunos'][] = Yii::$app->arrayPicker->pick([$sol->aluno], ['id','nome','endereco','lat','lng','idEscola']);
        }

        $output['escolasDisponiveis'] = $escolasDisponiveis;
        $output['alunosCadastrados'] = $alunosCadastrados;
        return $output;
    }
    public function actionEscolas()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      
        //$post = json_decode(file_get_contents('php://input'), true);
        $escolas = Escola::find()->select(['id','nome']);

        $escolas = $escolas->all();
        
        
        foreach($escolas as $escola) {
            $escola->nome = $escola->nomeCompleto;
        }
        return $escolas;
    }

    public function actionEscolasDisponiveis($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $rota = $this->findModel($id);
        $escolasDisponiveis = [];
        foreach($rota->condutor->escolas as $escola) {
                $escolasDisponiveis[] = $escola->idEscola;
        }
        //$post = json_decode(file_get_contents('php://input'), true);
        $escolas = Escola::find()->select(['id','nome']);
        if ($escolasDisponiveis)
            $escolas->where(['in', 'id', $escolasDisponiveis]);
        $escolas = $escolas->all();
        
        
        foreach($escolas as $escola) {
            $escola->nome = $escola->nomeCompleto;
        }
        return $escolas;
    }

    public function actionSalvarRota() {
        if(!isset($_POST['pontosCadastrados'])) 
            return ['status' => false, 'message' => 'Envie os pontos cadastrados'];

        return $_POST['pontosCadastrados'];
    }
    protected function findModel($id)
    {
        if (($model = CondutorRota::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}